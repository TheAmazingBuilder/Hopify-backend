<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Models\DoctorSchedule;
use App\Modules\Patient\Models\Patient;
use App\Modules\Hr\Models\Employee;
use Stancl\Tenancy\Database\Models\Tenant;
use Carbon\Carbon;

uses()->group('scheduling', 'crud');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->tenant->run(function () {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.cancel',
            'schedules.view', 'schedules.create',
        ]);
        $this->user->assignRole('receptionist');
        $this->actingAs($this->user, 'sanctum');
    });
});

test('authenticated user can list appointments', function () {
    $this->tenant->run(function () {
        Appointment::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/appointments');
        $response->assertStatus(200);
    });
});

test('can create appointment within doctor schedule', function () {
    $this->tenant->run(function () {
        $patient = Patient::factory()->create();
        $doctor = Employee::factory()->create();
        DoctorSchedule::factory()->create([
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => now()->addDay()->format('w'),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $start = now()->addDay()->setTime(10, 0, 0);
        $end = $start->copy()->addMinutes(30);

        $response = $this->postJson('/api/v1/appointments', [
            'patient_uuid' => $patient->uuid,
            'doctor_uuid' => $doctor->uuid,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
            'type' => 'consultation',
            'reason' => 'Checkup',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');
    });
});

test('cannot create overlapping appointment', function () {
    $this->tenant->run(function () {
        $patient1 = Patient::factory()->create();
        $patient2 = Patient::factory()->create();
        $doctor = Employee::factory()->create();
        DoctorSchedule::factory()->create([
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => now()->addDay()->format('w'),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $start = now()->addDay()->setTime(10, 0, 0);
        $end = $start->copy()->addMinutes(30);

        Appointment::factory()->create([
            'patient_uuid' => $patient1->uuid,
            'doctor_uuid' => $doctor->uuid,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'confirmed',
        ]);

        $response = $this->postJson('/api/v1/appointments', [
            'patient_uuid' => $patient2->uuid,
            'doctor_uuid' => $doctor->uuid,
            'start_time' => $start->copy()->addMinutes(15)->toDateTimeString(),
            'end_time' => $end->copy()->addMinutes(15)->toDateTimeString(),
            'type' => 'consultation',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    });
});

test('cannot create appointment outside doctor schedule', function () {
    $this->tenant->run(function () {
        $patient = Patient::factory()->create();
        $doctor = Employee::factory()->create();
        DoctorSchedule::factory()->create([
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => now()->addDay()->format('w'),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $start = now()->addDay()->setTime(14, 0, 0);
        $end = $start->copy()->addMinutes(30);

        $response = $this->postJson('/api/v1/appointments', [
            'patient_uuid' => $patient->uuid,
            'doctor_uuid' => $doctor->uuid,
            'start_time' => $start->toDateTimeString(),
            'end_time' => $end->toDateTimeString(),
            'type' => 'consultation',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    });
});

test('can cancel appointment', function () {
    $this->tenant->run(function () {
        $appointment = Appointment::factory()->pending()->create();
        $response = $this->postJson("/api/v1/appointments/{$appointment->uuid}/cancel", [
            'cancellation_notes' => 'Patient request',
        ]);
        $response->assertStatus(200);
        $appointment->refresh();
        expect($appointment->status)->toBe('cancelled');
    });
});

test('can confirm appointment', function () {
    $this->tenant->run(function () {
        $appointment = Appointment::factory()->pending()->create();
        $response = $this->postJson("/api/v1/appointments/{$appointment->uuid}/confirm");
        $response->assertStatus(200);
        $appointment->refresh();
        expect($appointment->status)->toBe('confirmed');
    });
});

test('unauthenticated user cannot access scheduling endpoints', function () {
    $this->tenant->run(function () {
        $this->app['auth']->guard('sanctum')->logout();
        $response = $this->getJson('/api/v1/appointments');
        $response->assertStatus(401);
    });
});

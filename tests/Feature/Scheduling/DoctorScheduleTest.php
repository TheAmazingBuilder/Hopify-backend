<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Scheduling\Models\DoctorSchedule;
use App\Modules\Hr\Models\Employee;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('scheduling', 'doctor_schedule');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->tenant->run(function () {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['schedules.view', 'schedules.create', 'schedules.update']);
        $this->actingAs($this->user, 'sanctum');
    });
});

test('can create doctor schedule', function () {
    $this->tenant->run(function () {
        $doctor = Employee::factory()->create();
        $response = $this->postJson('/api/v1/doctor-schedules', [
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.day_of_week', 1)
            ->assertJsonPath('data.day_name', 'Lundi');
    });
});

test('cannot create schedule with invalid day', function () {
    $this->tenant->run(function () {
        $doctor = Employee::factory()->create();
        $response = $this->postJson('/api/v1/doctor-schedules', [
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => 7,
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['day_of_week']);
    });
});

test('cannot create schedule with end before start', function () {
    $this->tenant->run(function () {
        $doctor = Employee::factory()->create();
        $response = $this->postJson('/api/v1/doctor-schedules', [
            'doctor_uuid' => $doctor->uuid,
            'day_of_week' => 1,
            'start_time' => '17:00',
            'end_time' => '08:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_time']);
    });
});

<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Policies\AppointmentPolicy;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('scheduling', 'security', 'policy');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->policy = new AppointmentPolicy();
});

test('receptionist can confirm pending appointment', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->assignRole('receptionist');
        $appointment = Appointment::factory()->pending()->create();
        expect($this->policy->confirm($user, $appointment))->toBeTrue();
    });
});

test('doctor can complete confirmed appointment', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->assignRole('doctor');
        $appointment = Appointment::factory()->confirmed()->create();
        expect($this->policy->complete($user, $appointment))->toBeTrue();
    });
});

test('cannot complete already cancelled appointment', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->assignRole('doctor');
        $appointment = Appointment::factory()->cancelled()->create();
        expect($this->policy->complete($user, $appointment))->toBeFalse();
    });
});

test('cannot cancel already completed appointment', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->givePermissionTo('appointments.cancel');
        $appointment = Appointment::factory()->completed()->create();
        expect($this->policy->cancel($user, $appointment))->toBeFalse();
    });
});

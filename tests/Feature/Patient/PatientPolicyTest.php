<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Policies\PatientPolicy;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('patient', 'security', 'policy');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->policy = new PatientPolicy();
});

test('user with patients.view permission can view any patients', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->givePermissionTo('patients.view');
        expect($this->policy->viewAny($user))->toBeTrue();
    });
});

test('user without patients.view permission cannot view any patients', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        expect($this->policy->viewAny($user))->toBeFalse();
    });
});

test('user can view patient within same tenant with permission', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->givePermissionTo('patients.view');
        $patient = Patient::factory()->create();
        expect($this->policy->view($user, $patient))->toBeTrue();
    });
});

test('user cannot view patient from different tenant even with permission', function () {
    $otherTenant = Tenant::factory()->create();
    $patient = $otherTenant->run(fn () => Patient::factory()->create());

    $this->tenant->run(function () use ($patient) {
        $user = User::factory()->create();
        $user->givePermissionTo('patients.view');
        expect($this->policy->view($user, $patient))->toBeFalse();
    });
});

test('admin can delete patient within same tenant', function () {
    $this->tenant->run(function () {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $patient = Patient::factory()->create();
        expect($this->policy->delete($user, $patient))->toBeTrue();
    });
});

test('secretary cannot view medical record', function () {
    $this->tenant->run(function () {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');
        $patient = Patient::factory()->create();
        expect($this->policy->viewMedicalRecord($secretary, $patient))->toBeFalse();
    });
});
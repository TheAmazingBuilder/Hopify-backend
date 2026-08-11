<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Patient\Models\Patient;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('tenancy', 'security');

beforeEach(function () {
    $this->tenantA = Tenant::factory()->create();
    $this->tenantB = Tenant::factory()->create();

    $this->userA = $this->tenantA->run(function () {
        return User::factory()->create();
    });

    $this->userB = $this->tenantB->run(function () {
        return User::factory()->create();
    });
});

test('a patient created in tenant A is not accessible from tenant B', function () {
    $patientUuid = $this->tenantA->run(function () {
        $patient = Patient::factory()->create();
        return $patient->uuid;
    });

    $this->tenantB->run(function () use ($patientUuid) {
        $found = Patient::find($patientUuid);
        expect($found)->toBeNull();
    });
});

test('patient listing from tenant A does not include tenant B patients', function () {
    $this->tenantA->run(function () {
        Patient::factory()->count(3)->create();
    });

    $this->tenantB->run(function () {
        Patient::factory()->count(2)->create();
    });

    $this->tenantA->run(function () {
        expect(Patient::count())->toBe(3);
    });
});

test('search scope respects tenant isolation', function () {
    $this->tenantA->run(function () {
        Patient::factory()->create(['fname' => 'John', 'lname' => 'Doe']);
    });

    $this->tenantB->run(function () {
        Patient::factory()->create(['fname' => 'John', 'lname' => 'Smith']);
        $results = Patient::search('John')->get();
        expect($results)->toHaveCount(1)
            ->and($results->first()->lname)->toBe('Smith');
    });
});

test('user from tenant A cannot view patient from tenant B via policy', function () {
    $patientB = $this->tenantB->run(function () {
        return Patient::factory()->create();
    });

    $this->tenantA->run(function () use ($patientB) {
        $user = User::factory()->create();
        $user->givePermissionTo('patients.view');

        $policy = new \App\Modules\Patient\Policies\PatientPolicy();
        expect($policy->view($user, $patientB))->toBeFalse();
    });
});

test('BelongsToTenant trait auto-sets tenant_id on create', function () {
    $this->tenantA->run(function () {
        $patient = Patient::create([
            'fname' => 'Jane', 'lname' => 'Doe',
            'dob' => '1990-01-01', 'gender' => 'female',
        ]);
        expect($patient->tenant_id)->toBe(tenant('id'));
    });
});

test('withoutTenant macro bypasses tenant scope', function () {
    $this->tenantA->run(function () { Patient::factory()->create(); });
    $this->tenantB->run(function () { Patient::factory()->create(); });

    tenancy()->end();
    expect(Patient::withoutTenant()->count())->toBe(2);
});
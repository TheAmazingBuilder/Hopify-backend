<?php

declare(strict_types=1);

use App\Modules\Foundation\Models\User;
use App\Modules\Patient\Models\Patient;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('patient', 'crud');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();

    $this->tenant->run(function () {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['patients.view', 'patients.create', 'patients.update']);
        $this->actingAs($this->user, 'sanctum');
    });
});

test('authenticated user can list patients', function () {
    $this->tenant->run(function () {
        Patient::factory()->count(5)->create();
        $response = $this->getJson('/api/v1/patients');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['uuid', 'mrn', 'fname', 'lname', 'full_name']], 'links', 'meta']);
    });
});

test('patient list is scoped to current tenant', function () {
    $otherTenant = Tenant::factory()->create();
    $this->tenant->run(fn () => Patient::factory()->count(3)->create());
    $otherTenant->run(fn () => Patient::factory()->count(2)->create());

    $this->tenant->run(function () {
        $response = $this->getJson('/api/v1/patients');
        expect(count($response->json('data')))->toBe(3);
    });
});

test('authenticated user can create a patient', function () {
    $this->tenant->run(function () {
        $response = $this->postJson('/api/v1/patients', [
            'fname' => 'Alice', 'lname' => 'Smith',
            'dob' => '1985-06-15', 'gender' => 'female',
            'blood_type' => 'A+', 'email' => 'alice@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.fname', 'Alice');

        $this->assertDatabaseHas('patients', [
            'fname' => 'Alice', 'tenant_id' => tenant('id'),
        ]);
    });
});

test('creating patient without required fields returns validation error', function () {
    $this->tenant->run(function () {
        $response = $this->postJson('/api/v1/patients', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fname', 'lname', 'dob', 'gender']);
    });
});

test('viewing non-existent patient returns 404', function () {
    $this->tenant->run(function () {
        $response = $this->getJson('/api/v1/patients/non-existent-uuid');
        $response->assertStatus(404);
    });
});

test('unauthenticated user cannot access patient endpoints', function () {
    $this->tenant->run(function () {
        $this->app['auth']->guard('sanctum')->logout();
        $response = $this->getJson('/api/v1/patients');
        $response->assertStatus(401);
    });
});
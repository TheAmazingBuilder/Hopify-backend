<?php

declare(strict_types=1);

use App\Modules\Patient\Actions\CreatePatientAction;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Repositories\PatientRepositoryInterface;
use Stancl\Tenancy\Database\Models\Tenant;

uses()->group('patient', 'unit', 'action');

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->repository = Mockery::mock(PatientRepositoryInterface::class);
    $this->action = new CreatePatientAction($this->repository);
});

afterEach(function () {
    Mockery::close();
});

test('execute creates patient through repository', function () {
    $this->tenant->run(function () {
        $dto = new CreatePatientDTO(
            fname: 'John', lname: 'Doe',
            dob: '1990-01-01', gender: 'male',
        );

        $mockPatient = Patient::make(['uuid' => 'test-uuid', 'fname' => 'John', 'lname' => 'Doe']);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::subset(['fname' => 'John', 'lname' => 'Doe', 'gender' => 'male']))
            ->andReturn($mockPatient);

        $result = $this->action->execute($dto);
        expect($result)->toBeInstanceOf(Patient::class)
            ->and($result->fname)->toBe('John');
    });
});

test('execute auto-generates MRN when not provided', function () {
    $this->tenant->run(function () {
        $dto = new CreatePatientDTO(
            fname: 'Jane', lname: 'Smith',
            dob: '1985-05-05', gender: 'female', mrn: null,
        );

        $mockPatient = Patient::make(['uuid' => 'test-uuid-2']);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturnUsing(function (array $data) use ($mockPatient) {
                expect($data['mrn'])->toStartWith('PAT-');
                return $mockPatient;
            });

        $this->action->execute($dto);
    });
});

test('execute dispatches PatientCreated event', function () {
    $this->tenant->run(function () {
        \Illuminate\Support\Facades\Event::fake([
            \App\Modules\Patient\Events\PatientCreated::class,
        ]);

        $dto = new CreatePatientDTO(
            fname: 'Event', lname: 'Test',
            dob: '2000-01-01', gender: 'other',
        );

        $mockPatient = Patient::make(['uuid' => 'event-uuid']);

        $this->repository->shouldReceive('create')->once()->andReturn($mockPatient);
        $this->action->execute($dto);

        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Modules\Patient\Events\PatientCreated::class,
            fn ($event) => $event->patient->uuid === 'event-uuid'
        );
    });
});
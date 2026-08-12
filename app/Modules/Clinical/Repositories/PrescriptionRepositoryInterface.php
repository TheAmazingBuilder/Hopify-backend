<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\Prescription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Clinical\Models\PrescriptionItem;

interface PrescriptionRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?Prescription;

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForDoctor(
        string $doctorUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getActiveForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getDispensedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function create(array $data): Prescription;

    public function createItem(array $data): PrescriptionItem;

    public function update(
        string $uuid,
        array $data
    ): bool;
}
<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\ImagingOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ImagingOrderRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?ImagingOrder;

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForRadiologist(
        string $radiologistUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getPendingForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getCompletedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getCriticalForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function create(array $data): ImagingOrder;
}
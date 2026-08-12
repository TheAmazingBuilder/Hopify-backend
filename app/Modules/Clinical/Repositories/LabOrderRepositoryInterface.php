<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\LabOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Modules\Clinical\Models\LabOrderItem;
use App\Modules\Clinical\Models\LabResult;

interface LabOrderRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?LabOrder;

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForDoctor(
        string $doctorUuid,
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

    public function getWithAbnormalResults(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function create(array $data): LabOrder;

    public function createItem(array $data): LabOrderItem;

    public function update(
        string $uuid,
        array $data
    ): bool;

    public function updateItem(
        string $uuid,
        array $data
    ): bool;

    public function findItemByUuid(
        string $uuid
    ): ?LabOrderItem;

    public function createResult(array $data): LabResult;

    public function updateResult(
        string $uuid,
        array $data
    ): bool;
}
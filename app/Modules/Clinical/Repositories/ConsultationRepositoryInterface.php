<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Repositories;

use App\Modules\Clinical\Models\Consultation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ConsultationRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findByUuid(
        string $uuid,
        bool $withRelations = true
    ): ?Consultation;

    public function findForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findForDoctor(
        string $doctorUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getHistoryForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getFinalizedForPatient(
        string $patientUuid,
        int $perPage = 15
    ): LengthAwarePaginator;

    public function create(array $data): Consultation;
}
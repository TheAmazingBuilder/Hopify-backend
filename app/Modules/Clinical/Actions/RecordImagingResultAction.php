<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\Models\ImagingOrder;
use App\Modules\Clinical\Models\ImagingResult;
use App\Modules\Clinical\Repositories\ImagingOrderRepositoryInterface;
use App\Modules\Hr\Models\Employee;
use App\Shared\Enums\ImagingOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RecordImagingResultAction
{
    public function __construct(
        private ImagingOrderRepositoryInterface $repository,
    ) {
    }

    public function execute(
        string $imagingOrderUuid,
        string $employeeUuid,
        ?string $filePath = null,
        ?string $thumbnailPath = null,
    ): ImagingResult {
        $order = $this->repository->findByUuid(
            $imagingOrderUuid,
            true
        );

        if (! $order) {
            throw ValidationException::withMessages([
                'imaging_order_uuid' =>
                    'The specified imaging order does not exist.',
            ]);
        }

        if (
            $order->status === ImagingOrderStatus::Cancelled
        ) {
            throw ValidationException::withMessages([
                'imaging_order_uuid' =>
                    'A cancelled imaging order cannot receive a result.',
            ]);
        }

        $radiologist = Employee::query()
            ->whereKey($employeeUuid)
            ->where('is_active', true)
            ->first();

        if (! $radiologist) {
            throw ValidationException::withMessages([
                'employee_uuid' =>
                    'The specified employee does not exist or is inactive.',
            ]);
        }

        return DB::transaction(
            function () use (
                $order,
                $radiologist,
                $filePath,
                $thumbnailPath
            ): ImagingResult {
                $result = ImagingResult::create([
                    'imaging_order_uuid' => $order->uuid,
                    'radiologist_uuid' => $radiologist->uuid,
                    'file_path' => $filePath,
                    'thumbnail_path' => $thumbnailPath,
                    'resulted_at' => now(),
                ]);

                $this->repository->update(
                    $order->uuid,
                    [
                        'status' => ImagingOrderStatus::Completed,
                    ]
                );

                return $result->load([
                    'imagingOrder.patient',
                    'radiologist',
                ]);
            }
        );
    }
}
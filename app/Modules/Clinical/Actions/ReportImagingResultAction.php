<?php

declare(strict_types=1);

namespace App\Modules\Clinical\Actions;

use App\Modules\Clinical\Models\ImagingResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReportImagingResultAction
{
    public function execute(
        string $imagingResultUuid,
        string $radiologistUuid,
        string $report,
        ?string $impression = null,
        bool $isCritical = false,
    ): ImagingResult {
        $result = ImagingResult::query()
            ->with([
                'imagingOrder.patient',
                'radiologist',
            ])
            ->find($imagingResultUuid);

        if (! $result) {
            throw ValidationException::withMessages([
                'imaging_result_uuid' =>
                    'The specified imaging result does not exist.',
            ]);
        }

        if (
            (string) $result->radiologist_uuid
            !== $radiologistUuid
        ) {
            throw ValidationException::withMessages([
                'radiologist_uuid' =>
                    'The specified radiologist is not assigned to this result.',
            ]);
        }

        if ($result->reported_at !== null) {
            throw ValidationException::withMessages([
                'imaging_result_uuid' =>
                    'The imaging result has already been reported.',
            ]);
        }

        if (trim($report) === '') {
            throw ValidationException::withMessages([
                'report' =>
                    'The imaging report cannot be empty.',
            ]);
        }

        return DB::transaction(
            function () use (
                $result,
                $report,
                $impression,
                $isCritical
            ): ImagingResult {
                $result->update([
                    'report' => $report,
                    'impression' => $impression,
                    'is_critical' => $isCritical,
                    'reported_at' => now(),
                ]);

                return $result->fresh([
                    'imagingOrder.patient',
                    'imagingOrder.orderedBy',
                    'radiologist',
                ]);
            }
        );
    }
}
<?php

declare(strict_types=1);

namespace App\Shared\Support\Permissions;

final class ClinicalPermissions
{
    public const CONSULTATIONS_VIEW = 'clinical.consultations.view';
    public const CONSULTATIONS_CREATE = 'clinical.consultations.create';
    public const CONSULTATIONS_UPDATE = 'clinical.consultations.update';
    public const CONSULTATIONS_FINALIZE = 'clinical.consultations.finalize';

    public const PRESCRIPTIONS_VIEW = 'clinical.prescriptions.view';
    public const PRESCRIPTIONS_CREATE = 'clinical.prescriptions.create';
    public const PRESCRIPTIONS_CANCEL = 'clinical.prescriptions.cancel';
    public const PRESCRIPTIONS_DISPENSE = 'clinical.prescriptions.dispense';

    public const LABS_VIEW = 'clinical.labs.view';
    public const LABS_CREATE = 'clinical.labs.create';
    public const LAB_RESULTS_RECORD = 'clinical.labs.results.record';
    public const LAB_RESULTS_VALIDATE = 'clinical.labs.results.validate';

    public const IMAGING_VIEW = 'clinical.imaging.view';
    public const IMAGING_CREATE = 'clinical.imaging.create';
    public const IMAGING_RESULTS_RECORD = 'clinical.imaging.results.record';
    public const IMAGING_RESULTS_REPORT = 'clinical.imaging.results.report';

    public static function all(): array
    {
        return [
            self::CONSULTATIONS_VIEW,
            self::CONSULTATIONS_CREATE,
            self::CONSULTATIONS_UPDATE,
            self::CONSULTATIONS_FINALIZE,

            self::PRESCRIPTIONS_VIEW,
            self::PRESCRIPTIONS_CREATE,
            self::PRESCRIPTIONS_CANCEL,
            self::PRESCRIPTIONS_DISPENSE,

            self::LABS_VIEW,
            self::LABS_CREATE,
            self::LAB_RESULTS_RECORD,
            self::LAB_RESULTS_VALIDATE,

            self::IMAGING_VIEW,
            self::IMAGING_CREATE,
            self::IMAGING_RESULTS_RECORD,
            self::IMAGING_RESULTS_REPORT,
        ];
    }
}
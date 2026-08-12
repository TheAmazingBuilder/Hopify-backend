<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum PrescriptionStatus: string
{
    case Active = 'active';
    case Dispensed = 'dispensed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}

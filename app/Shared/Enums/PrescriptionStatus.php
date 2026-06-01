<?php

namespace App\Shared\Enums;

enum PrescriptionStatus: string
{
    case Active = 'active';
    case Dispensed = 'dispensed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}

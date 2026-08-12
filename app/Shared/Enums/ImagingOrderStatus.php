<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum ImagingOrderStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
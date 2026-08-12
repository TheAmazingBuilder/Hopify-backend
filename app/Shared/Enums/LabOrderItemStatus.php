<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum LabOrderItemStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
}
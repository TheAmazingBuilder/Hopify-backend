<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum LabOrderPriority: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Stat = 'stat';
}

<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum LabAbnormalityLevel: string
{
    case Low = 'low';
    case High = 'high';
    case Critical = 'critical';
}
<?php

namespace App\Shared\Enums;

enum ImagingUrgency: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Emergency = 'emergency';
}

<?php

namespace App\Shared\Enums;

enum AllergySeverity: string
{
    case Mild = 'mild';
    case Moderate = 'moderate';
    case Severe = 'severe';
    case LifeThreatening = 'life_threatening';
}

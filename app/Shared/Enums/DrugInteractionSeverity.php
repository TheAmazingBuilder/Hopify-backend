<?php

namespace App\Shared\Enums;

enum DrugInteractionSeverity: string
{
    case Minor = 'minor';
    case Moderate = 'moderate';
    case Major = 'major';
    case Contraindicated = 'contraindicated';
}

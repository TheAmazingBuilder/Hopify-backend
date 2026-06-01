<?php

namespace App\Shared\Enums;

enum DiagnosisType: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Differential = 'differential';
}

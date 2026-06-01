<?php

namespace App\Shared\Enums;

enum AntecedentType: string
{
    case Medical = 'medical';
    case Surgical = 'surgical';
    case Family = 'family';
    case Obstetric = 'obstetric';
    case Psychiatric = 'psychiatric';
    case Other = 'other';
}

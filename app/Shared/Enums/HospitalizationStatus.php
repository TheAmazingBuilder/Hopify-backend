<?php

namespace App\Shared\Enums;

enum HospitalizationStatus: string
{
    case Active = 'active';
    case Discharged = 'discharged';
    case Transferred = 'transferred';
    case Deceased = 'deceased';
}

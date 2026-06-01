<?php

namespace App\Shared\Enums;

enum MedicationForm: string
{
    case Tablet = 'tablet';
    case Capsule = 'capsule';
    case Liquid = 'liquid';
    case Injection = 'injection';
    case Cream = 'cream';
    case Patch = 'patch';
    case Inhaler = 'inhaler';
    case Drops = 'drops';
    case Suppository = 'suppository';
    case Powder = 'powder';
}

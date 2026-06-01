<?php

namespace App\Shared\Enums;

enum DepartmentType: string
{
    case General = 'general';
    case Emergency = 'emergency';
    case ICU = 'icu';
    case Surgery = 'surgery';
    case Maternity = 'maternity';
    case Pediatrics = 'pediatrics';
    case Oncology = 'oncology';
    case Cardiology = 'cardiology';
    case Radiology = 'radiology';
    case Laboratory = 'laboratory';
    case Pharmacy = 'pharmacy';
    case Outpatient = 'outpatient';
}

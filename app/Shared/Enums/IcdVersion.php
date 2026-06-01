<?php

namespace App\Shared\Enums;

enum IcdVersion: string
{
    case Icd10 = 'ICD-10';
    case Icd11 = 'ICD-11';
}

<?php

namespace App\Shared\Enums;

enum MedicationRoute: string
{
    case Oral = 'oral';
    case Intravenous = 'intravenous';
    case Intramuscular = 'intramuscular';
    case Subcutaneous = 'subcutaneous';
    case Topical = 'topical';
    case Inhalation = 'inhalation';
    case Rectal = 'rectal';
    case Sublingual = 'sublingual';
}

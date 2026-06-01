<?php

namespace App\Shared\Enums;

enum InvoiceType: string
{
    case Consultation = 'consultation';
    case Hospitalization = 'hospitalization';
    case Pharmacy = 'pharmacy';
    case Laboratory = 'laboratory';
    case Imaging = 'imaging';
    case Miscellaneous = 'miscellaneous';
}

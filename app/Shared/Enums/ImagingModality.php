<?php

namespace App\Shared\Enums;

enum ImagingModality: string
{
    case XRay = 'x_ray';
    case CTScan = 'ct_scan';
    case MRI = 'mri';
    case Ultrasound = 'ultrasound';
    case PETScan = 'pet_scan';
    case Mammography = 'mammography';
    case Fluoroscopy = 'fluoroscopy';
}

<?php

namespace App\Shared\Enums;

enum BloodType: string
{
    case A_Pos = 'A+';
    case A_Neg = 'A-';
    case B_Pos = 'B+';
    case B_Neg = 'B-';
    case AB_Pos = 'AB+';
    case AB_Neg = 'AB-';
    case O_Pos = 'O+';
    case O_Neg = 'O-';
    case Unknown = 'unknown';
}

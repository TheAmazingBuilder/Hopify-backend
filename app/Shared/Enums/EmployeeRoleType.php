<?php

namespace App\Shared\Enums;

enum EmployeeRoleType: string
{
    case Doctor = 'doctor';
    case Nurse = 'nurse';
    case Technician = 'technician';
    case Pharmacist = 'pharmacist';
    case Receptionist = 'receptionist';
    case Admin = 'admin';
    case Director = 'director';
}

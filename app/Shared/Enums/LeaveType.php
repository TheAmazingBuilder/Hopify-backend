<?php

namespace App\Shared\Enums;

enum LeaveType: string
{
    case Vacation = 'vacation';
    case Sick = 'sick';
    case Maternity = 'maternity';
    case Paternity = 'paternity';
    case Emergency = 'emergency';
    case Unpaid = 'unpaid';
}

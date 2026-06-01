<?php

namespace App\Shared\Enums;

enum TransactionType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Expired = 'expired';
}

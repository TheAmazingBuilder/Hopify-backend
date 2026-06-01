<?php

namespace App\Shared\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Insurance = 'insurance';
    case Transfer = 'bank_transfer';
    case Check = 'check';
    case Online = 'online';
}

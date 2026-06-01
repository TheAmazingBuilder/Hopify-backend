<?php

namespace App\Shared\Enums;

enum TenantPlan: string
{
    case Starter = 'starter';
    case Pro = 'pro';
    case Enterprise = 'enterprise';
}

<?php

namespace App\Shared\Enums;

enum BedStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';
    case Reserved = 'reserved';
}

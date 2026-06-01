<?php

namespace App\Shared\Enums;

enum LabPriority: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Stat = 'stat';
}

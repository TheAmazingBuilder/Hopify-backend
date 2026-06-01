<?php

namespace App\Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatientCreated
{
    use Dispatchable, SerializesModels;
    public function __construct(public $patient) {}
}

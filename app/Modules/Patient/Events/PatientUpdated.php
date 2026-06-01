<?php

namespace App\Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatientUpdated
{
    use Dispatchable, SerializesModels;
    public function __construct(public $patient) {}
}

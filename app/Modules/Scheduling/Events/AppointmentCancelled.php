<?php

namespace App\Modules\Scheduling\Events;

use App\Modules\Scheduling\Models\Appointment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $reason
    ) {}
}

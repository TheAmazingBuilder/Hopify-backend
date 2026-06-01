<?php

namespace App\Modules\Scheduling\Actions;

use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Repositories\AppointmentRepositoryInterface;
use App\Modules\Scheduling\Events\AppointmentCancelled;
use App\Modules\Foundation\Models\AuditLog;

class CancelAppointmentAction
{
    public function __construct(
        protected AppointmentRepositoryInterface $repository
    ) {}

    public function execute(string $uuid, string $reason): Appointment
    {
        $appointment = Appointment::where('uuid', $uuid)->firstOrFail();
        
        $appointment->update([
            'status' => 'cancelled',
            'notes' => $appointment->notes . "\nCancellation Reason: " . $reason
        ]);

        // Déclenchement de l'événement pour les notifications (Email/SMS)
        event(new AppointmentCancelled($appointment, $reason));

        // Audit
        AuditLog::record('appointment.cancelled', $appointment, ['reason' => $reason]);

        return $appointment;
    }
}

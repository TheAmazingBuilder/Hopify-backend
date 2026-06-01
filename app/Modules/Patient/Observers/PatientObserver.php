<?php

namespace App\Modules\Patient\Observers;

use App\Modules\Patient\Models\Patient;
use App\Modules\Foundation\Models\AuditLog;

class PatientObserver
{
    /**
     * Gère l'événement "updated" du Patient.
     */
    public function updated(Patient $patient): void
    {
        // Enregistrement de l'audit automatique lors d'une modification
        AuditLog::record('patient.updated', $patient, [
            'changes' => $patient->getChanges()
        ]);
    }


    /**
     * Gère l'événement "created" du Patient.
     */
    public function created(Patient $patient): void
    {
        AuditLog::record('patient.created', $patient);
    }
}

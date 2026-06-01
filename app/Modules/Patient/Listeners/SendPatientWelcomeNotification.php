<?php

namespace App\Modules\Patient\Listeners;

use App\Modules\Patient\Events\PatientCreated;

class SendPatientWelcomeNotification
{
    public function handle(PatientCreated $event): void {}
}

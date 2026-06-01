<?php

namespace App\Modules\Scheduling\Services;

use App\Modules\Scheduling\Models\Appointment;
use App\Modules\Scheduling\Models\DoctorSchedule;
use Carbon\Carbon;

class SchedulingService
{
    /**
     * Vérifie si un créneau est disponible pour un médecin (incluant le Buffer Time).
     */
    public function isSlotAvailable(string $doctorUuid, string $start, string $end, int $bufferMinutes = 15): bool
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        // On élargit la fenêtre de recherche avec le buffer pour détecter les conflits de proximité
        $bufferedStart = (clone $startTime)->subMinutes($bufferMinutes);
        $bufferedEnd = (clone $endTime)->addMinutes($bufferMinutes);

        // 1. Vérifier les chevauchements avec le buffer
        $conflict = Appointment::where('doctor_uuid', $doctorUuid)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($bufferedStart, $bufferedEnd) {
                $query->where('start_time', '<', $bufferedEnd)
                      ->where('end_time', '>', $bufferedStart);
            })
            ->exists();

        if ($conflict) {
            return false;
        }

        // 2. Vérifier si le créneau est dans les horaires de travail (sans buffer pour le travail)
        return $this->isWithinWorkingHours($doctorUuid, $startTime, $endTime);
    }

    /**
     * Vérifie si le créneau est dans les horaires de travail.
     */
    protected function isWithinWorkingHours(string $doctorUuid, Carbon $start, Carbon $end): bool
    {
        $dayOfWeek = $start->dayOfWeek; // 0 (Sun) - 6 (Sat)
        $timeStart = $start->toTimeString();
        $timeEnd = $end->toTimeString();

        return DoctorSchedule::where('doctor_uuid', $doctorUuid)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $timeStart)
            ->where('end_time', '>=', $timeEnd)
            ->exists();
    }
}

<?php

namespace App\Shared\Exceptions;

use Exception;

class AppointmentConflictException extends Exception
{
    protected $message = 'The doctor is already booked for this time slot or it is outside working hours.';
    protected $code = 422;
}

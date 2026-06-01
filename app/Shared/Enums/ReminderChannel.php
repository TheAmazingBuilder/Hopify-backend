<?php

namespace App\Shared\Enums;

enum ReminderChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
    case Push = 'push';
    case WhatsApp = 'whatsapp';
}

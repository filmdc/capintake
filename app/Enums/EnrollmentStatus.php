<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Completed = 'completed';
    case Withdrawn = 'withdrawn';
    case Denied = 'denied';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Withdrawn => 'Withdrawn',
            self::Denied => 'Denied',
        };
    }
}

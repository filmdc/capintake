<?php

declare(strict_types=1);

namespace App\Enums;

enum EmploymentStatus: string
{
    case Employed = 'employed';
    case EmployedPartTime = 'employed_part_time';
    case Unemployed = 'unemployed';
    case Retired = 'retired';
    case Disabled = 'disabled';
    case Student = 'student';
    case Homemaker = 'homemaker';
    case SelfEmployed = 'self_employed';

    public function label(): string
    {
        return match ($this) {
            self::Employed => 'Employed (Full-Time)',
            self::EmployedPartTime => 'Employed (Part-Time)',
            self::Unemployed => 'Unemployed',
            self::Retired => 'Retired',
            self::Disabled => 'Disabled',
            self::Student => 'Student',
            self::Homemaker => 'Homemaker',
            self::SelfEmployed => 'Self-Employed',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum IncomeFrequency: string
{
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';
    case Annually = 'annually';
    case OneTime = 'one_time';

    public function label(): string
    {
        return match ($this) {
            self::Weekly => 'Weekly',
            self::Biweekly => 'Bi-Weekly',
            self::Monthly => 'Monthly',
            self::Annually => 'Annually',
            self::OneTime => 'One-Time',
        };
    }

    public function annualMultiplier(): float
    {
        return match ($this) {
            self::Weekly => 52,
            self::Biweekly => 26,
            self::Monthly => 12,
            self::Annually => 1,
            self::OneTime => 1,
        };
    }
}

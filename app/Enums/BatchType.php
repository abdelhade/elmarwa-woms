<?php

declare(strict_types=1);

namespace App\Enums;

enum BatchType: string
{
    case Project = 'project';
    case DailyShift = 'daily_shift';

    public function label(): string
    {
        return match ($this) {
            self::Project => 'مشروع / دفعة',
            self::DailyShift => 'يومي / وردية',
        };
    }
}

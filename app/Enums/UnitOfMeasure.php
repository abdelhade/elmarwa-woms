<?php

declare(strict_types=1);

namespace App\Enums;

enum UnitOfMeasure: string
{
    case Piece = 'piece';
    case Kilogram = 'kg';
    case Meter = 'meter';
    case Liter = 'liter';
    case SquareMeter = 'sqm';
    case Box = 'box';

    public function label(): string
    {
        return match ($this) {
            self::Piece => 'قطعة',
            self::Kilogram => 'كيلوجرام',
            self::Meter => 'متر',
            self::Liter => 'لتر',
            self::SquareMeter => 'متر مربع',
            self::Box => 'صندوق',
        };
    }

    public function abbreviation(): string
    {
        return match ($this) {
            self::Piece => 'pc',
            self::Kilogram => 'kg',
            self::Meter => 'm',
            self::Liter => 'L',
            self::SquareMeter => 'm²',
            self::Box => 'box',
        };
    }
}

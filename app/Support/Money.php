<?php

namespace App\Support;

class Money
{
    public static function toCents(string|int|float $value): int
    {
        $parts = explode('.', (string) $value, 2);
        $intPart = (int) $parts[0];
        $decPart = $parts[1] ?? '0';
        $decPart = substr(str_pad($decPart, 2, '0'), 0, 2);

        return ($intPart * 100) + (int) $decPart;
    }

    public static function formatBrl(string|int|float $value): string
    {
        $cents = is_int($value) ? $value : self::toCents($value);
        $negative = $cents < 0;
        $cents = abs($cents);

        $intPart = intdiv($cents, 100);
        $decPart = $cents % 100;
        $intFormatted = number_format($intPart, 0, '', '.');

        return ($negative ? '-' : '') . $intFormatted . ',' . str_pad((string) $decPart, 2, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Support;

class Money
{
    public static function toCents(string|int|float $value): int
    {
        return (int) bcmul((string) $value, '100', 0);
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

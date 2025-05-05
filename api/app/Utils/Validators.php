<?php

namespace App\Utils;

class Validators
{
    public static function isValidCpf(string $input): bool
    {
        $input = preg_replace('/\D/', '', $input);
        if (strlen($input) != 11) return false;

        $firstSum = 0;
        $secondSum = 0;

        for ($i = 0, $j = 10; $i < 9; $i++, $j--)
            $firstSum += (int)$input[$i] * $j;


        $firstDigit = $firstSum % 11;
        $firstDigit = ($firstDigit >= 2) ? 11 - $firstDigit : 0;

        for ($i = 0, $j = 11; $i < 10; $i++, $j--)
            $secondSum += (int)$input[$i] * $j;

        $secondDigit = $secondSum % 11;
        $secondDigit = ($secondDigit >= 2) ? 11 - $secondDigit : 0;

        return ((string)$firstDigit == $input[9] && (string)$secondDigit == $input[10]);
    }

    public static function isRenavamValid(string $input)
    {
        $input = preg_replace('/\D/', '', $input);
        if (strlen($input) != 11) return false;
        $soma = 0;
        $pesos = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 10; $i++)
            $soma += $pesos[$i] * (int)$input[$i];

        $digit = $soma % 11;
        $digit = ($digit >= 2) ? 11 - $digit : 0;
        return (string)$digit == $input[10];
    }
}

<?php

namespace App\Utils;

use DateTime;
use Exception;

class TransformData
{
    public static function stringInteger(?string $value, int $default): int
    {
        $int_value = intval($value);
        if ($int_value == 0) $int_value = $default;
        return $int_value;
    }

    public static function stringToDateTime(?string $string): ?DateTime
    {
        if ($string === null) return null;

        if (!preg_match('/^([0-2][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/', $string)) {
            return null;
        }

        $date = DateTime::createFromFormat('d/m/Y', $string);
        $errors = DateTime::getLastErrors();


        if ($errors) {
            if ($errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                return null;
            }
        }


        return $date;
    }
}

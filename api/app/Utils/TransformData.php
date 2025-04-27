<?php

namespace App\Utils;

use DateTime;
use Exception;

use function PHPUnit\Framework\returnSelf;

class TransformData
{
    public static function stringInteger(?string $value, int $default): int
    {
        $int_value = intval($value);
        if ($int_value == 0) $int_value = $default;
        return $int_value;
    }

    public static function stringBoolean(?string $value, bool $default)
    {
        if (!$value)
            return $default;
        else if ($value ==  'true')
            return true;
        else if ($value ==  'false')
            return false;
        else return $default;
    }

    public  static function extractNumbers(string $texto): string
    {
        return preg_replace('/\D/', '', $texto);
    }

    public static function stringToDateTime(?string $string): ?DateTime
    {
        if ($string === null) return null;

        // Verifica se a string está no formato yyyy-mm-dd
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $string)) {
            return null;
        }

        $date = DateTime::createFromFormat('Y-m-d', $string);
        $errors = DateTime::getLastErrors();

        if ($errors) {
            if ($errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                return null;
            }
        }

        return $date;
    }
}

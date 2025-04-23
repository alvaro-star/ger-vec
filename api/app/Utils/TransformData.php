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

        try {
            return new DateTime($string);
        } catch (Exception $e) {
            return null;
        }
    }
}

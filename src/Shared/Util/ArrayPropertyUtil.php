<?php

declare(strict_types=1);

namespace App\Shared\Util;

class ArrayPropertyUtil
{
    public static function getProperty($data, $key, $default = null)
    {
        if (!is_array($data)) {
            return $default;
        }

        return (array_key_exists($key, $data) && null !== $data[$key]) ? $data[$key] : $default;
    }
}

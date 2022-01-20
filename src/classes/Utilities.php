<?php

namespace KanbanBoard;

/**
 * Utilities module
 * To load utilities.
 */
class Utilities
{

    /**
     * @param $name
     * @param $default
     * @return array|mixed|string|void
     */
    public static function env($name, $default = null)
    {
        $value = getenv($name);
        if ($default !== null) {
            if (!empty($value)) {
                return $value;
            }
            return $default;
        }
        return (empty($value)) ? die('Environment variable ' . $name . ' not found or has no value') : $value;
    }

    /**
     * @param $array
     * @param $key
     * @return bool
     */
    public static function hasValue($array, $key): bool
    {
        return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
    }
}

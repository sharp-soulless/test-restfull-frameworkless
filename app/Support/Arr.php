<?php

namespace App\Support;

class Arr
{
    /**
     * @param $array
     *
     * @return array
     */
    public static function flatten($array): array
    {
        $flattenArray = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $flattenArray[] = $item;
            } else {
                $flattenArray = array_merge($flattenArray, self::flatten($item));
            }
        }

        return $flattenArray;
    }

    /**
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    public static function only(array $array, array $keys): array
    {
        $data = [];

        foreach ($array as $key => $value) {
            if (in_array($key, $keys, true)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
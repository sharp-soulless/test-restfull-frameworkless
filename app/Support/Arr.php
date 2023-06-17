<?php

namespace App\Support;

class Arr
{
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
}
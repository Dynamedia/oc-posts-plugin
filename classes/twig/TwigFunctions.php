<?php

namespace Dynamedia\Posts\Classes\Twig;

class TwigFunctions
{
    public static function getFunctions()
    {
        return [
            'extractRepeaterData' => [self::class, 'extractRepeaterData']
        ];
    }

    /*
    * Takes repeater data and converts to key/value array
    */
    public static function extractRepeaterData($data)
    {
        $keyVal = [];
        if (is_array($data)) {
            foreach ($data as $item) {
                $keyVal[$item['key']] = $item['value'];
            }
        }
        return $keyVal;

    }
}

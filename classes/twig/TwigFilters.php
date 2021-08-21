<?php

namespace Dynamedia\Posts\Classes\Twig;

class TwigFilters
{
    public static function getFilters()
    {
        return [
            'modelToArray' => [self::class, 'modelToArray'],
            'extractUrlParam' => [self::class, 'extractUrlParam'],
        ];
    }

    /*
     * Extracts the given parameter from an url
     */
    public static function extractUrlParam($data, $param)
    {
        $query = !empty(parse_url($data)['query']) ? parse_url($data)['query'] : false;
        if ($query) {
            parse_str($query, $params);
        }
        return !empty($params[$param]) ? $params[$param] : null;
    }


    public static function modelToArray($data)
    {
        return;
        return $data->toArray();
    }
}

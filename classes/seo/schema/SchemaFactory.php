<?php

namespace Dynamedia\Posts\Classes\Seo\Schema;

use Cms\Classes\Theme;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Graph;

class SchemaFactory
{
    public static function makeSpatie($type)
    {
        $type = lcfirst($type);
        return Schema::{$type}();
    }

    public static function makeNative($type, $model, $data = [])
    {
        $type = ucfirst($type);
        $className = "\Dynamedia\Posts\Classes\Seo\Schema\\" . $type;
        return new $className($model, $data);
    }

    public static function makeBase()
    {
        $graph = new Graph();

        $themeSettings = Theme::getActiveTheme()->operator;
        $typeOption = !empty($themeSettings['operator_type']) ? $themeSettings['operator_type'] : 'organization';
        $publisher = static::makeSpatie($typeOption)
            ->setProperty('@id', \Url::to('/'). "/#pubisher");

        $publisherAddress = static::makeSpatie('postalAddress')
            ->addressCountry($themeSettings['address_country'])
            ->addressLocality($themeSettings['address_city'])
            ->addressRegion($themeSettings['address_region'])
            ->postalCode($themeSettings['address_region'])
            ->streetAddress($themeSettings['address_street']);

        $publisherGeo = static::makeSpatie('geoCoordinates')
            ->latitude($themeSettings['address_latitude'])
            ->longitude($themeSettings['address_longitude']);

        $publisher->name($themeSettings['name'])
            ->address($publisherAddress)
            ->geo($publisherGeo)
            ->url(\Url::to('/'));

        $website = static::makeSpatie('website')
            ->setProperty('@id', \Url::to('/'). "/#website");


        $graph->add($publisher, 'publisher');
        $graph->add($website, 'website');

        return $graph;
    }
}

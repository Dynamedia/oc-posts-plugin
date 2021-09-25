<?php

namespace Dynamedia\Posts\Classes\Seo\Schema;

use Cms\Classes\Theme;
use Spatie\SchemaOrg\Schema;


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

}

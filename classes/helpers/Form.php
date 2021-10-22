<?php
namespace Dynamedia\Posts\Classes\Helpers;
use Config;
use Dynamedia\Posts\Classes\Body\Templatebody\TemplateBody;
use Lang;
use Cms\Classes\Theme;
use Cms\Classes\Layout;
use Cms\Classes\Partial;
use Cms\Classes\Content;
use Cache;

class Form
{
    public static function getCmsContentOptions()
    {
        $options = [];
        $content = Content::listInTheme(Theme::getActiveTheme(), true);
        foreach ($content as $item) {
            $options[$item->fileName] = $item->fileName;
        }
        return $options;
    }

    public static function getCmsPartialOptions()
    {
        $options = [];
        $partial = Partial::listInTheme(Theme::getActiveTheme(), true);
        foreach ($partial as $item) {
            if (starts_with($item->fileName, 'postbody')) {
                $options[$item->fileName] = $item->fileName;
            }
        }
        return $options;
    }

    public static function getCmsLayoutOptions()
    {
        $options = [
            '__inherit__' => 'Inherit'
        ];

        $layout = Layout::listInTheme(Theme::getActiveTheme(), true);
        foreach ($layout as $item) {
            if ($item->postsLayout) {
                $options[$item->fileName] = $item->description;
            }
        }
        $options[''] = 'None';
        return $options;
    }

    public static function getImageStyleOptions()
    {
        return [
        'inline-left' => 'Inline Left',
        'inline-right' => 'Inline Right',
        'full-above' => 'Above',
        'full-below' => 'Below',
        ];
    }

    public static function getDefaultPostListSortOptions()
    {
        return [
            'published_at desc' => Lang::get('dynamedia.posts::lang.common.dropdown.newest_first'),
            'published_at asc'  => Lang::get('dynamedia.posts::lang.common.dropdown.oldest_first'),
            'updated_at desc'   => Lang::get('dynamedia.posts::lang.common.dropdown.recent_update'),
            '__random__'        => Lang::get('dynamedia.posts::lang.common.dropdown.random')
        ];
    }

    public static function getPostListSortOptions()
    {
        $inherit = ['__inherit__' => 'Inherit'];
        return array_merge($inherit, static::getDefaultPostListSortOptions());
    }

    public static function getDefaultPostListIncludeSubCategoriesOptions()
    {
        return [
            false => Lang::get('dynamedia.posts::lang.common.dropdown.no'),
            true  => Lang::get('dynamedia.posts::lang.common.dropdown.yes')
        ];
    }

    public static function getPostListIncludeSubCategoriesOptions()
    {
        $inherit = ['__inherit__' => 'Inherit'];
        return array_merge($inherit, static::getDefaultPostListIncludeSubCategoriesOptions());
    }

    public static function getDefaultPostListPerPageOptions()
    {
        $perPage = [];
        $min = Config::get('dynamedia.posts::postsListMinPerPage');
        $max = Config::get('dynamedia.posts::postsListMaxPerPage');

        for ($i = $min; $i <= $max; $i++) {
            $perPage[$i] = $i;
        }
        return $perPage;
    }

    public static function getPostListPerPageOptions()
    {
        $appended = ['__inherit__' => 'Inherit'];
        $default  = static::getDefaultPostListPerPageOptions();

        foreach ($default as $entry) {
            $appended[$entry] = $entry;
        }
        return $appended;
    }

    /**
     * List all .yml files in the active theme body_templates directory
     *
     * @return array
     * @throws \ApplicationException
     */
    public static function getBodyTemplateOptions()
    {
        $cacheKey = "dynamedia_posts_post_body_templates";
        $templateDir = "/body_templates/";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $options = [];

        $path = Theme::getActiveTheme()->getPath() . $templateDir;

        $files = [];
        if (file_exists($path)) {
            $files = collect(\File::allfiles($path))->filter(function($value) {
                return $value->getExtension() == 'yml';
            });
        }

        foreach ($files as $file) {
            try {
                $config = TemplateBody::parseConfig($file->getPathName());
            } catch (\Exception $e) {
                continue;
            }
            if (!empty($config['name'])) {
                $name = $config['name'];
            } else {
                $name = $file->getFilename();
            }

            $options[$templateDir . $file->getFilename()] = $name;
        }

        if (config('app.debug') == false) {
            Cache::forever($cacheKey, $options);
        }

        return $options;
    }

    public static function getHours()
    {
        $result = [];

        for ($i = 0; $i <= 24; $i++) {
            $result[$i] = $i;
        }
        return $result;
    }

    public static function getMinutes()
    {
        $result = [];

        for ($i = 0; $i <= 60; $i++) {
            $result[$i] = $i;
        }
        return $result;
    }

    public static function getSeconds()
    {
        return static::getMinutes();
    }

}

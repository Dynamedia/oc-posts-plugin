<?php
namespace Dynamedia\Posts\Classes\Helpers;
use Config;
use Cms\Classes\Theme;
use Cms\Classes\Layout;
use Cms\Classes\Partial;
use Cms\Classes\Content;

class Form
{
    public static function getCmsContentOptions()
    {
        $content = Content::listInTheme(Theme::getActiveTheme(), true);
        foreach ($content as $item) {
            $options[$item->fileName] = $item->fileName;
        }
        return $options;
    }

    public static function getCmsPartialOptions()
    {
        $partial = Partial::listInTheme(Theme::getActiveTheme(), true);
        foreach ($partial as $item) {
            $options[$item->fileName] = $item->fileName;
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
            $options[$item->fileName] = $item->description;
        }
        $options[''] = 'None';
        return $options;
    }

    public static function getImageStyleOptions()
    {
        return Config::get('dynamedia.posts::postSectionImageDropdown');
    }

    public static function getDefaultPostListSortOptions()
    {
        return [
            'published_at desc' => 'Newest First',
            'published_at asc'  => 'Oldest First',
            'updated_at desc'   => 'Recently Updated',
            '__random__'        => 'Random'
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
            false => 'No',
            true => 'Yes'
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

    public static function getMicroCacheDuration()
    {
        $min = Config::get('dynamedia.posts::microCacheMinDuration');
        $max = Config::get('dynamedia.posts::microCacheMaxDuration');

        for ($i = $min; $i <= $max; $i++) {
            $perPage[$i] = $i;
        }
        return $perPage;
    }
}
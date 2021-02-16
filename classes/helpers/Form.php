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

    public static function getComponentSortOptions()
    {
        return [
            'published_at desc' => 'Newest First',
            'published_at asc'  => 'Oldest First',
            'updated_at desc'   => 'Recently Updated',
            '__random__'            => 'Random'
        ];
    }
}
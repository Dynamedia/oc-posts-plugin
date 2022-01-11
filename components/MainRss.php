<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Rss\RssAll;

/**
 * MainRss Component
 */
class MainRss extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.main_rss.name',
            'description' => 'dynamedia.posts::lang.components.main_rss.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $feed = new RssAll();
        return $feed->makeViewResponse();
    }
}

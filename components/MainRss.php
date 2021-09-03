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
            'name' => 'MainRss Component',
            'description' => 'Provides a method to dictate the url for the main rss feed'
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

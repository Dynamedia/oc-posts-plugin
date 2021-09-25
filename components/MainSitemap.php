<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Seo\Schema\ExtendedGraph;
use Dynamedia\Posts\Classes\Sitemap\SitemapAll;



/**
 * MainSitemap Component
 */
class MainSitemap extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'MainSitemap Component',
            'description' => 'Provides a method to dictate the url for the main sitemap'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $graph = \App::make('dynamedia.posts.graph');
        dd($graph->toArray());
        $feed = new SitemapAll();
        return $feed->makeViewResponse();
    }
}

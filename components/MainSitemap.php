<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Sitemap\SitemapAll;
use Cms\Classes\Theme;
use App;
use Dynamedia\Posts\Classes\Seo\Schema\SchemaFactory;


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
        $graph = SchemaFactory::makeBase();
        dd($graph);
        $feed = new SitemapAll();
        return $feed->makeViewResponse();
    }
}

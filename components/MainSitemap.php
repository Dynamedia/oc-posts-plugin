<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Sitemap\SitemapAll;
use Cms\Classes\Theme;
use App;
use Dynamedia\Posts\Classes\Seo\Schema\SchemaFactory;
use Rainlab\Translate\Classes\Translator;

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
        $graph = App::make('dynamedia.posts.graph');
        $graph->article()->setProperty('@id', Translator::instance()->getPathInLocale('/', 'de'));
        dd($graph->toArray());
        $operator = Theme::getActiveTheme()->operator['operator_type'];
        $test = SchemaFactory::makeSpatie($operator);
        dd($test);
        $feed = new SitemapAll();
        return $feed->makeViewResponse();
    }
}

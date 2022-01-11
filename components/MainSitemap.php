<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Seo\Schema\ExtendedGraph;
use Dynamedia\Posts\Classes\Sitemap\SitemapAll;
use Dynamedia\Posts\Models\Post;


/**
 * MainSitemap Component
 */
class MainSitemap extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.main_sitemap.name',
            'description' => 'dynamedia.posts::lang.components.main_sitemap.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $feed = new SitemapAll();
        return $feed->makeViewResponse();
    }
}

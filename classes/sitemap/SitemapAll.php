<?php namespace Dynamedia\Posts\Classes\Sitemap;

use View;
use Response;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Models\Category;

class SitemapAll
{
    private $tags;
    private $categories;

    public function __construct()
    {
        $this->categories = Category::all();
        $this->tags = Tag::all();
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::sitemap.sitemap_list', [
            'categories'    => $this->categories,
            'tags'          => $this->tags,
        ]);

        return Response::make($view)
            ->header('Content-Type', 'application/xml');
    }
}

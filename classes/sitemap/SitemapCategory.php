<?php namespace Dynamedia\Posts\Classes\Sitemap;

use View;
use Response;
use Dynamedia\Posts\Models\Post;

class SitemapCategory
{
    private $category;
    private $postsList;

    public function __construct($category)
    {
        $this->category = $category;
        $this->postsList = Post::getPostsList([
            'optionsCategoryIds'    => [$this->category->id]
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::sitemap.sitemap_posts', [
            'parent'    => $this->category,
            'posts'     => $this->postsList['items'],
        ]);

        return Response::make($view)
            ->header('Content-Type', 'application/xml');
    }
}

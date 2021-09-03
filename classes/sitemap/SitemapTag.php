<?php namespace Dynamedia\Posts\Classes\Sitemap;

use View;
use Response;
use Dynamedia\Posts\Models\Post;

class SitemapTag
{
    private $tag;
    private $postsList;

    public function __construct($tag)
    {
        $this->tag = $tag;
        $this->postsList = Post::getPostsList([
            'optionsTagIds'    => [$this->tag->id]
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::sitemap.sitemap_posts', [
            'parent'    => $this->tag,
            'posts'     => $this->postsList['items'],
        ]);

        return Response::make($view)
            ->header('Content-Type', 'application/xml');
    }
}

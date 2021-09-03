<?php namespace Dynamedia\Posts\Classes\Rss;

use Dynamedia\Posts\Models\Post;
use RainLab\Translate\Classes\Translator;
use View;
use Response;

class RssAll
{
    private $postsList;

    public function __construct()
    {
        $this->postsList = Post::getPostsList([
            'optionsLimit'     => 10,
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::rss.all_rss', [
            'posts'         => $this->postsList['items'],
            'language'      => Translator::instance()->getLocale(),
            'title'         => "The title",
            'description'   => "The Description",
            'link'          => "/"
            ]);

        return Response::make($view)
            ->header('Content-Type', 'application/rss+xml');
    }



}

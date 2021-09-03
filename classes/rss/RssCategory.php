<?php namespace Dynamedia\Posts\Classes\Rss;

use Dynamedia\Posts\Models\Post;
use RainLab\Translate\Classes\Translator;
use View;
use Response;

class RssCategory
{
    private $category;
    private $postsList;

    public function __construct($category)
    {
        $this->category = $category;
        $this->postsList = Post::getPostsList([
            'optionsLimit'          => 10,
            'optionsCategoryIds'    => [$this->category->id]
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::rss.category_rss', [
            'category'  => $this->category,
            'posts'     => $this->postsList['items'],
            'language'  => Translator::instance()->getLocale()
            ]);

        return Response::make($view)
            ->header('Content-Type', 'application/rss+xml');
    }



}

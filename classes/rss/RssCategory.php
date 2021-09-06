<?php namespace Dynamedia\Posts\Classes\Rss;

use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Settings;
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
            'optionsLimit'          => (int) Settings::instance()->get('rssPostCount'),
            'optionsPerPage'        => (int) Settings::instance()->get('rssPostCount'),
            'optionsCategoryIds'    => [$this->category->id]
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::rss.category_rss', [
            'category'      => $this->category,
            'posts'         => $this->postsList['items'],
            'language'      => Translator::instance()->getLocale(),
            'title_prefix'  => Settings::instance()->getTranslateAttribute('rssTitle')
            ]);

        return Response::make($view)
            ->header('Content-Type', 'application/rss+xml');
    }



}

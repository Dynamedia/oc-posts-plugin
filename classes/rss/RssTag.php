<?php namespace Dynamedia\Posts\Classes\Rss;

use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Settings;
use RainLab\Translate\Classes\Translator;
use View;
use Response;

class RssTag
{
    private $tag;
    private $postsList;

    public function __construct($tag)
    {
        $this->tag = $tag;
        $this->postsList = Post::getPostsList([
            'optionsLimit'      => (int) Settings::instance()->get('rssPostCount'),
            'optionsPerPage'    => (int) Settings::instance()->get('rssPostCount'),
            'optionsTagIds'     => [$this->tag->id]
        ]);
    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::rss.tag_rss', [
            'tag'           => $this->tag,
            'posts'         => $this->postsList['items'],
            'language'      => Translator::instance()->getLocale(),
            'title_prefix'  => Settings::instance()->getTranslateAttribute('rssTitle')
            ]);

        return Response::make($view)
            ->header('Content-Type', 'application/rss+xml');
    }



}

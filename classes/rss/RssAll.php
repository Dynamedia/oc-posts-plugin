<?php namespace Dynamedia\Posts\Classes\Rss;

use Cms\Classes\Controller;
use Cms\Classes\Page;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Settings;
use RainLab\Translate\Classes\Translator;
use View;
use Response;

class RssAll
{
    private $postsList;

    public function __construct()
    {
        $this->postsList = Post::getPostsList([
            'optionsLimit'     => (int) Settings::instance()->get('rssPostCount'),
            'optionsPerPage'     => (int) Settings::instance()->get('rssPostCount'),
        ]);

    }

    public function makeViewResponse()
    {
        $view = View::make('dynamedia.posts::rss.all_rss', [
            'posts'         => $this->postsList['items'],
            'language'      => Translator::instance()->getLocale(),
            'title'         => Settings::instance()->getTranslateAttribute('rssTitle'),
            'description'   => Settings::instance()->getTranslateAttribute('rssDescription'),
            'atom_link'     => Controller::getController()->currentPageUrl()
            ]);

        return Response::make($view)
            ->header('Content-Type', 'application/rss+xml');
    }



}

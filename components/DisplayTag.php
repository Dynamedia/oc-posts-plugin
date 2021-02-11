<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Tag;
use Lang;

class DisplayTag extends ComponentBase
{
    public $tag = null;

    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'Display Tag',
            'description' => 'Display a tag with posts'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsLimit' => [
                'title'             => 'Total posts',
                'description'       => 'Limit the number of posts to fetch',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'Please enter a positive whole number or leave blank',
                'default'           => '',
                'showExternalParam' => false,
            ],
            'postsPerPage' => [
                'title'             => 'Posts per page',
                'description'       => 'Limit the number of posts per page',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'Please enter a positive whole number',
                'default'           => '10',
                'showExternalParam' => false,
            ],
            'noPostsMessage' => [
                'title'             => 'rainlab.posts::lang.settings.posts_no_posts',
                'description'       => 'rainlab.posts::lang.settings.posts_no_posts_description',
                'type'              => 'string',
                'default'           => Lang::get('rainlab.posts::lang.settings.posts_no_posts_default'),
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'       => 'rainlab.posts::lang.settings.posts_order',
                'description' => 'rainlab.posts::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
                'showExternalParam' => false,
            ],
        ];
    }

    public function onRun()
    {
        $this->setTag();

        if (!$this->tag) return $this->controller->run('404');

        $this->setPosts();
    }

    private function setTag()
    {
        $this->tag = Tag::where('slug', $this->param('slug'))->first();
    }

    public function setPosts()
    {
        $options = [
            'limit' => (int) $this->property('postsLimit'),
            'perPage' => (int) $this->property('postsPerPage'),
        ];

        $this->posts = $this->tag->getPosts($options);
    }
}

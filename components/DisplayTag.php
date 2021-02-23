<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Helpers\Form;
use Dynamedia\Posts\Models\Tag;
use Lang;
use App;

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
                'title'             => 'No Posts Message',
                'description'       => 'Message to display when no posts are found',
                'type'              => 'string',
                'default'           => "No posts found",
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'       => 'Sort Order',
                'description' => 'Sort the fetched posts',
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
        if (App::bound('dynamedia.posts.tag')) {
            $this->tag = App::make('dynamedia.posts.tag');
        }
    }

    public function setPosts()
    {
        $options = [
            'limit' => (int) $this->property('postsLimit'),
            'perPage' => (int) $this->property('postsPerPage'),
            'sort'   => $this->property('sortOrder')
        ];

        $this->posts = $this->tag->getPosts($options);
    }

    public function getSortOrderOptions()
    {
        return Form::getComponentSortOptions();
    }
}

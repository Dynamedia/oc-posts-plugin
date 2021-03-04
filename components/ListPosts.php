<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Helpers\Form;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Traits\PaginationTrait;
use App;
use Lang;

class ListPosts extends ComponentBase
{
    use PaginationTrait;

    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'ListPosts Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'categoryFilter' => [
                'title'         => 'Category Filter',
                'description'   => 'Restrict results to this category',
                'type'          => 'dropdown',
                'default'       => '',
                'showExternalParam' => false
            ],
            'includeSubcategories' => [
                'title'       => 'Include Subcategories',
                'description' => 'List posts from subcategories of selected category',
                'type'        => 'checkbox',
                'showExternalParam' => false,
            ],
            'tagFilter' => [
                'title'         => 'Tag Filter',
                'description'   => 'Restrict results to this tag',
                'type'          => 'dropdown',
                'default'       => '',
                'showExternalParam' => false
            ],
            'postIds' => [
                'title'             => 'Post Filter',
                'description'       => 'Restrict results to these post Id\'s',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'Please enter a comma separated list of post Id\'s',
                'default'           => '',
                'showExternalParam' => false
            ],
            'notPostIds' => [
                'title'             => 'Exclude Posts',
                'description'       => 'Exclude these post Id\'s',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'Please enter a comma separated list of post Id\'s',
                'default'           => '',
                'showExternalParam' => false
            ],
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

    public function getCategoryFilterOptions()
    {
        $options = [
            '' => "All"
        ];
        return array_merge($options, Category::all()->lists('name', 'id'));
    }

    public function getTagFilterOptions()
    {
        $options = [
            '' => "All"
        ];
        return array_merge($options, Tag::all()->lists('name', 'id'));
    }

    public function onRun()
    {
        $this->setPosts();
    }

    private function setPosts()
    {
        $postListOptions = [
            'optionsLimit'       => (int) $this->property('postsLimit'),
            'optionsPerPage'     => (int) $this->property('postsPerPage'),
            'optionsPage'        => $this->getRequestedPage(),
            'optionsSort'        => $this->property('sortOrder')
        ];

        if ($this->property('categoryFilter')) {
            $postListOptions['optionsCategoryIds'] = explode(",", $this->property('categoryFilter'));
        }

        if ($this->property('tagFilter')) {
            $postListOptions['optionsTagId'] = $this->property('categoryFilter');
        }

        if ($this->property('postIds')) {
            $postListOptions['optionsPostIds'] = explode(",", $this->property('postIds'));
        }

        $excludes = [];
        if (App::bound('dynamedia.posts.post')) {
            $self = App::make('dynamedia.posts.post');
            if (!empty($self->id)) {
                $excludes[] = $self->id;
            }
        }

        if ($this->property('notPostIds')) {
             $excludes = array_merge($excludes, explode(",", $this->property('notPostIds')));
        }


        $postListOptions['optionsNotPostIds'] = $excludes;

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }

    public function getSortOrderOptions()
    {
        return Form::getDefaultPostListSortOptions();
    }
}

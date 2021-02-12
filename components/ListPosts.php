<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Lang;

class ListPosts extends ComponentBase
{
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
        $options = [
            'categoryId' => $this->property('categoryFilter'),
            'tagId' => $this->property('tagFilter'),
            'subcategories' => (bool) $this->property('includeSubcategories'),
            'postIds' => $this->property('postIds'),
            'limit' => (int) $this->property('postsLimit'),
            'perPage' => (int) $this->property('postsPerPage'),
        ];

        $query = new Post();
        $this->posts = $query->getPostsList($options);
    }
}

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
            'name'        => 'dynamedia.posts::lang.components.list_posts.name',
            'description' => 'dynamedia.posts::lang.components.list_posts.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'categoryFilter' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.category_filter.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.category_filter.description',
                'type'          => 'dropdown',
                'default'       => '',
                'showExternalParam' => false
            ],
            'includeSubcategories' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.include_subcategories.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.include_subcategories.description',
                'type'        => 'checkbox',
                'showExternalParam' => false,
            ],
            'tagFilter' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.tag_filter.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.tag_filter.description',
                'type'          => 'dropdown',
                'default'       => '',
                'showExternalParam' => false
            ],
            'postIds' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.post_ids.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.post_ids.description',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.post_ids.validation',
                'default'           => '',
                'showExternalParam' => false
            ],
            'notPostIds' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.not_post_ids.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.not_post_ids.description',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.not_post_ids.validation',
                'default'           => '',
                'showExternalParam' => false
            ],
            'notCategoryIds' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.not_category_ids.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.not_category_ids.description',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.not_category_ids.validation',
                'default'           => '',
                'showExternalParam' => false
            ],
            'notTagIds' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.not_tag_ids.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.not_tag_ids.description',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.not_tag_ids.validation',
                'default'           => '',
                'showExternalParam' => false
            ],
            'postsLimit' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.posts_limit.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.posts_limit.description',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.posts_limit.validation',
                'default'           => '',
                'showExternalParam' => false,
            ],
            'postsPerPage' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.posts_per_page.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.posts_per_page.description',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'dynamedia.posts::lang.components.list_posts.properties.posts_per_page.validation',
                'default'           => '10',
                'showExternalParam' => false,
            ],
            'noPostsMessage' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.no_posts_message.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.no_posts_message.description',
                'type'              => 'string',
                'default'           => Lang::get('dynamedia.posts::lang.components.list_posts.properties.no_posts_message.default'),
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'         => 'dynamedia.posts::lang.components.list_posts.properties.sort_order.title',
                'description'   => 'dynamedia.posts::lang.components.list_posts.properties.sort_order.description',
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
            $postListOptions['optionsTagIds'] = explode(",", $this->property('tagFilter'));
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

        if ($this->property('notCategoryIds')) {
            $postListOptions['optionsNotCategoryIds'] = explode(",", $this->property('notCategoryIds'));
        }

        if ($this->property('notTagIds')) {
            $postListOptions['optionsNotTagIds'] = explode(",", $this->property('notTagIds'));
        }

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }

    public function getSortOrderOptions()
    {
        return Form::getDefaultPostListSortOptions();
    }
}

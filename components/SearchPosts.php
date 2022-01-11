<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Traits\PaginationTrait;
use Input;

class SearchPosts extends ComponentBase
{
    use PaginationTrait;

    public $posts;
    public $searchQuery;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.search_posts.name',
            'description' => 'dynamedia.posts::lang.components.search_posts.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsLimit' => [
                'title'         => 'dynamedia.posts::lang.components.search_posts.properties.posts_limit.title',
                'description'   => 'dynamedia.posts::lang.components.search_posts.properties.posts_limit.description',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'dynamedia.posts::lang.components.search_posts.properties.posts_limit.validation',
                'default'           => '',
                'showExternalParam' => false,
            ],
            'postsPerPage' => [
                'title'         => 'dynamedia.posts::lang.components.search_posts.properties.posts_per_page.title',
                'description'   => 'dynamedia.posts::lang.components.search_posts.properties.posts_per_page.description',
                'type'              => 'string',
                'validationPattern' => '^[1-9]\d*$',
                'validationMessage' => 'dynamedia.posts::lang.components.search_posts.properties.posts_per_page.validation',
                'default'           => '10',
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'         => 'dynamedia.posts::lang.components.search_posts.properties.sort_order.title',
                'description'   => 'dynamedia.posts::lang.components.search_posts.properties.sort_order.description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
                'showExternalParam' => false,
            ],
        ];
    }

    public function onRun()
    {
        $this->setSearchQuery();

        if (!$this->searchQuery) return redirect('/');

        $this->setPosts();
    }

    private function setPosts()
    {
        $postListOptions = [
            'optionsLimit'       => (int) $this->property('postsLimit'),
            'optionsPerPage'     => (int) $this->property('postsPerPage'),
            'optionsPage'        => $this->getRequestedPage(),
            'optionsSort'        => $this->property('sortOrder'),
            'optionsSearchQuery' => $this->searchQuery
        ];

        $postList = Post::getPostsList($postListOptions);
        $this->posts = $this->getPaginator($postList, $this->currentPageUrl())
            ->appends(["q" => $this->searchQuery]);
        }

    private function setSearchQuery()
    {
        $this->searchQuery = Input::get('q');
    }
}

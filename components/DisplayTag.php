<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Traits\PaginationTrait;
use Dynamedia\Posts\Models\Post;
use App;

class DisplayTag extends ComponentBase
{
    use PaginationTrait;

    public $tag;
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.display_tag.name',
            'description' => 'dynamedia.posts::lang.components.display_tag.description'
        ];
    }

    public function defineProperties()
    {
        return [
            // All component settings moved to backend settings area
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
        $postListOptions = [
            'optionsTagId'      => $this->tag->id,
            'optionsSort'       => $this->tag->post_list_sort,
            'optionsPage'       => $this->getRequestedPage(),
            'optionsPerPage'    => $this->tag->post_list_per_page
        ];

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }

}

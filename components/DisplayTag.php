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

        // Check that we are at the right url. If not, redirect and get back here.
        // ONLY if we're getting the tag from the URL
        if (!$this->tag) {
            return $this->controller->run('404');
        }
        if ($this->currentPageUrl() != $this->tag->url) {
            return redirect($this->tag->url, 301);
        }

        $this->tag->setSeo();

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
            'optionsTagIds'     => [$this->tag->id],
            'optionsSort'       => $this->tag->post_list_sort,
            'optionsPage'       => $this->getRequestedPage(),
            'optionsPerPage'    => $this->tag->post_list_per_page
        ];

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }

}

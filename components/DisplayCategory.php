<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Traits\PaginationTrait;
use App;

class DisplayCategory extends ComponentBase
{
    use PaginationTrait;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.display_category.name',
            'description' => 'dynamedia.posts::lang.components.display_category.description'
        ];
    }

    public $category = null;
    public $posts;
    private $defer;


    public function defineProperties()
    {
        return [
            // All component settings moved into backend settings area.
        ];
    }

    public function onRun()
    {
        $this->setCategory();

        // Check that we are at the right url. If not, redirect and get back here.
        // ONLY if we're getting the category from the URL
            if (!$this->category) {
                if (!$this->deferToPostComponent()) return $this->controller->run('404');
                $this->defer = true;
                return;
            }
            if ($this->currentPageUrl() != $this->category->url) {
                return redirect($this->category->url, 301);
            }

        $this->setPosts();
    }

    public function onRender()
    {
        // Return an empty string and avoid rendering the markup. todo Find a better way?
        if ($this->defer) return " ";
    }

    /**
     * Check if there is a displayPost component after this one
     * and that it will process from the URL paramater
     *
     * @return bool
     **/
    private function deferToPostComponent() {
        $components = collect($this->page->components)
            ->filter(function($c) {
               if ($c->alias == $this->alias) return true;
               if ($c->name == 'displayPost') return true;
            });
            // true if this component is first or other component was successful
            if ($components->count() == 2
                && ($components->first()->alias == $this->alias
                    || !empty($components->first()->post))) {
                return true;
            }
            return false;
    }


    private function setCategory()
    {
        if (App::bound('dynamedia.posts.category')) {
            $this->category = App::make('dynamedia.posts.category');
        }
    }

    public function setPosts()
    {
        $postListOptions = [
            'optionsCategoryIds' => $this->category->post_list_ids,
            'optionsSort'        => $this->category->post_list_sort,
            'optionsPage'        => $this->getRequestedPage(),
            'optionsPerPage'     => $this->category->post_list_per_page,
        ];

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }
}

<?php namespace Dynamedia\Posts\Components;

use Carbon\Translator;
use Cms\Classes\ComponentBase;
use BackendAuth;
use App;
use Dynamedia\Posts\Traits\PaginationTrait;
use Input;


class DisplayPost extends ComponentBase
{
    use PaginationTrait;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.display_post.name',
            'description' => 'dynamedia.posts::lang.components.display_post.description'
        ];
    }

    public $post;
    public $paginator;
    private $defer;

    public function defineProperties()
    {
        return [

        ];
    }

    public function onRun()
    {
        $this->setPost();

        // 404 if post not found
        if (!$this->post) {
           if (!$this->deferToCategoryComponent()) return $this->controller->run('404');
            $this->defer = true;
            return;
        }
        // Check that we are at the right url. If not, redirect and get back here.
        if ($this->currentPageUrl() != $this->post->url) {
            return redirect($this->post->url, 301);
        }
        // Check publishing status and permissions
        if (!$this->post->is_published) {
            $user = BackendAuth::getUser();
            if (!$user || !$user->id == $this->post->author->id || !$user->hasAccess(['edit_all_unpublished_posts']) ) {
                try {
                return $this->controller
                    ->setStatusCode(403)->run('403');
                } catch (\Exception $e) {
                    return response("Not authorised", 403);
                }
            }
        }

        $this->setPaginator();
    }

    /**
     * Check if there is a displayCategory component after this one
     * and that it will process from the URL parameter
     *
     * @return bool
     **/
    private function deferToCategoryComponent() {
        $components = collect($this->page->components)
            ->filter(function($c) {
               if ($c->alias == $this->alias) return true;
               if ($c->name == 'displayCategory') return true;
            });

            // true if this component is first or other component was successful
            if ($components->count() == 2
                && ($components->first()->alias == $this->alias
                    || !empty($components->first()->category))) {
                return true;
            }
            return false;
    }

    public function onRender()
    {
        // Return an empty string and avoid rendering the markup. todo Find a better way?
        if ($this->defer) return " ";
    }

    public function getRequestedPage()
    {
        return (int) Input::get('page') > 0 ? (int) Input::get('page') : 1;
    }

    private function setPost()
    {
        if (App::bound('dynamedia.posts.post')) {
            $this->post = App::make('dynamedia.posts.post');
        }
    }

    private function setPaginator()
    {
        if (empty($this->post->pages)) return;

        $paginatorOptions = [
            'items'         => $this->post->pages[$this->getRequestedPage() - 1],
            'totalResults'  => count($this->post['pages']),
            'itemsPerPage'  => 1,
            'requestedPage' => $this->getRequestedPage()
        ];

        $this->paginator = $this->getPaginator($paginatorOptions, $this->currentPageUrl());
    }

}

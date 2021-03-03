<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use BackendAuth;
use Lang;
use Str;
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Input;


class DisplayPost extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Display Post',
            'description' => 'Display a post'
        ];
    }

    public $category;
    public $post;
    public $defer;

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
            if (! BackendAuth::getUser() || ! $this->post->userCanEdit(BackendAuth::getUser())) {
                try {
                return $this->controller
                    ->setStatusCode(403)->run('403');
                } catch (\Exception $e) {
                    return response("Not authorised", 403);
                }
            }
        }
    }

    /**
     * Check if there is a displayCategory component after this one
     * and that it will process from the URL paramater
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

    public function getCurrentPage()
    {
        return $this->post->pages[$this->getRequestedPage() - 1];
    }

    public function getPaginator()
    {
        $paginator = new LengthAwarePaginator(
            $this->getCurrentPage(),
            count($this->post->pages),
            1,
            $this->getRequestedPage()
        );
        return $paginator->withPath($this->currentPageUrl());
    }
}

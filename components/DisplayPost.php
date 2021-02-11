<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Dynamedia\Posts\Models\Post;
use Lang;
use Str;
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public $content;
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

    private function getSlug()
    {
        if ($this->param('slug')) return $this->param('slug');
        return $this->param('postslug');
    }
    
    private function getRequestedPage()
    {
        return Input::get('page') ? Input::get('page') : 1;
    }

    // todo implement category check
    private function setPost()
    {
        if (App::bound('dynamedia.post')) {
            $this->post = App::make('dynamedia.post');
        }
    }

    public function getContentsList()
    {
        if ($this->post) {
            return $this->post->getContentsList($this->currentPageUrl());
        }
        return [];
    }

    public function getPaginator()
    {
        if ($this->post) {
            return $this->post->getPaginator($this->currentPageUrl());
        }
    }
}

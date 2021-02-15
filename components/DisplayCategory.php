<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Category;
use Cms\Classes\Page;
use Dynamedia\Posts\Models\Post;
use Lang;
use Str;
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DisplayCategory extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Display Category',
            'description' => 'Display a category with posts'
        ];
    }

    public $category = null;

    public $categoryIds = [];

    public $posts;

    public $defer;


    public function defineProperties()
    {
        return [
            'includeSubcategories' => [
                'title'       => 'Include Subcategories',
                'description' => 'List posts from subcategories of selected category',
                'type'        => 'checkbox',
                'showExternalParam' => false,
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
        // We now should have the category if specified - get a list of ID's

        if ($this->category) $this->categoryIds[] = $this->category->id;

        if ($this->property('categoryFilter') == "__fromList__" && $this->property('categoryIds')) {
            $this->categoryIds = explode(',', $this->property('categoryIds'));
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

//    public function getCategoryFilterOptions()
//    {
//        // Default empty option
//        $baseOptions = [
//            '__fromURL__' => 'From URL Param',
//            '' => 'All',
//            '__fromList__' => 'From List'
//            ];
//
//        $categories =  Category::orderBy('name', 'asc')
//            ->get()
//            ->pluck('name','slug')
//            ->toArray();
//
//        return array_merge($baseOptions, $categories);
//    }


    private function setCategory()
    {
        if (App::bound('dynamedia.category')) {
            $this->category = App::make('dynamedia.category');
        }
    }


    public function setPosts()
    {
        $options = [
            'subcategories' => (bool) $this->property('includeSubcategories'),
            'limit' => (int) $this->property('postsLimit'),
            'perPage' => (int) $this->property('postsPerPage'),
        ];

        $this->posts = $this->category->getPosts($options);
    }

}
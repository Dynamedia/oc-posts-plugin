<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Models\Category;
use Cms\Classes\Page;
use Dynamedia\Posts\Models\Post;
use Lang;
use Str;
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListPosts extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'listPosts Component',
            'description' => 'No description provided yet...'
        ];
    }

    // When dealing with a single category
    public $category = null;

    public $categoryIds = [];

    public $posts;

    public $defer;


    public function defineProperties()
    {
        return [

            'categoryFilter' => [
                'title'       => 'Filter by category',
                'description' => 'List posts from category',
                'type'        => 'dropdown',
                'showExternalParam' => false,
            ],
            'categoryIds' => [
                'title'       => 'Category ID\'s',
                'description' => 'List posts from these category ID\'s',
                'type'        => 'string',
                'validationPattern' => '^\d+(,\d+)*$',
                'validationMessage' => 'rainlab.posts::lang.settings.posts_per_page_validation',
                'showExternalParam' => false,
            ],
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
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.posts::lang.settings.posts_per_page_validation',
                'default'           => '',
            ],
            'postsPerPage' => [
                'title'             => 'Posts per page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.posts::lang.settings.posts_per_page_validation',
                'default'           => '10',
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
            ],
        ];
    }

    public function onRun()
    {
        $this->setCategory();

        // Check that we are at the right url. If not, redirect and get back here.
        // ONLY if we're getting the category from the URL
        if ($this->property('categoryFilter') == "__fromURL__") {
            if (!$this->category) {
                if (!$this->deferToShowComponent()) return $this->controller->run('404');
                $this->defer = true;
                return;
            }
            if ($this->currentPageUrl() != $this->category->url) {
                return redirect($this->category->url, 301);
            }
        }

        // We now should have the category if specified - get a list of ID's

        if ($this->category) $this->categoryIds[] = $this->category->id;

        if ($this->property('categoryFilter') == "__fromList__" && $this->property('categoryIds')) {
            $this->categoryIds = explode(',', $this->property('categoryIds'));
        }


        $this->getPosts();
    }

    public function onRender()
    {
        // Return an empty string and avoid rendering the markup. todo Find a better way?
        if ($this->defer) return " ";
    }

    /**
     * Check if there is a showPost component after this one
     * and that it will process from the URL paramater
     * 
     * @return bool
     **/
    private function deferToShowComponent() {
        $components = collect($this->page->components)
            ->filter(function($c) {
               if ($c->alias == $this->alias
                   && $c->property('categoryFilter') == '__fromURL__') return true;
               if ($c->name == 'showPost') return true;
            });
            // true if this component is first or other component was successful
            if ($components->count() == 2 
                && ($components->first()->alias == $this->alias
                    || !empty($components->first()->post))) {
                return true;
            }
            return false;
    }

    public function getCategoryFilterOptions()
    {
        // Default empty option
        $fromUrl = ['__fromURL__' => 'From URL Param'];
        $all = ['' => 'All'];
        $fromList = ['__fromList__' => 'From List'];


        $categories =  Category::orderBy('name', 'asc')
            ->get()
            ->pluck('name','slug')
            ->toArray();

        return array_merge($fromUrl, $all, $fromList, $categories);
    }


    private function setCategory()
    {
        if (App::bound('dynamedia.category')) {
            $this->category = App::make('dynamedia.category');
        } else {
            $this->category = Category::where('slug', $this->param('category'))->first();
        }
    }


    public function getPosts()
    {
        $options = [
            'categoryIds' => $this->categoryIds,
            'subcategories' => (bool) $this->property('includeSubcategories'),
            'limit' => (int) $this->property('postsLimit'),
        ];

        $posts = new Post();

        $this->posts = $posts->listFrontEnd($options)
            ->get();

        $this->posts->each(function ($post) {
          //  $post->setUrl($this->controller, []);
        });

    }

}

<?php namespace Dynamedia\Posts;

use Backend;
use Lang;
use System\Classes\PluginBase;
use App;
use Event;
use Str;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Category;

/**
 * posts Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.plugin.name',
            'description' => 'dynamedia.posts::lang.plugin.description',
            'author'      => 'Dynamedia',
            'icon'        => 'icon-pencil-square-o'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        \Cms\Models\ThemeData::extend(function ($model) {
            $model->addJsonable('images');
        });

        Event::listen('cms.page.beforeDisplay', function ($controller, $url, $page) {
            $slug = $controller->param('slug');
            $displayCategory = null;
            $displayPost = null;

            // Check if we have a component capable of changing the layout
            if ($slug && !empty($page->settings['components'])) {

                $displayCategory = collect($page->settings['components'])
                    ->filter(function ($v, $k) {
                        if (Str::startsWith($k, "displayCategory")) return true;
                    });

                $displayPost = collect($page->settings['components'])
                    ->filter(function ($v, $k) {
                        if (Str::startsWith($k, "displayPost")) return true;
                    });
            }

            if ($displayCategory) {
                $category = Category::where('slug', $slug)->first();

                if ($category && $category->cms_layout != "__inherit__") {
                    $page->layout = $category->cms_layout;
                }

                App::instance('dynamedia.category', $category);
            }

            if ($displayPost) {
                $post = Post::where('slug', $slug)
                    ->with('primary_category', 'tags')
                    ->first();

                if ($post && $post->cms_layout != "__inherit__") {
                    $page->layout = $post->cms_layout;
                } elseif ($post && !empty($post->primary_category) && $post->primary_category->cms_layout != "__inherit__") {
                    $page->layout = $post->primary_category->cms_layout;
                }

                App::instance('dynamedia.post', $post);
            }
    });
        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'posts-category'       => 'Posts Category',
                'all-posts-categories' => 'All Posts Categories',
                'posts-post'           => 'Posts Post',
                'all-posts-posts'      => 'All Posts Posts',
                'category-posts-posts' => 'Category Posts Posts',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'posts-category' || $type == 'all-posts-categories') {
                return Category::getMenuTypeInfo($type);
            }
            elseif ($type == 'posts-post' || $type == 'all-posts-posts' || $type == 'category-posts-posts') {
                return Post::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'posts-category' || $type == 'all-posts-categories') {
                return Category::resolveMenuItem($item, $url, $theme);
            }
            elseif ($type == 'posts-post' || $type == 'all-posts-posts' || $type == 'category-posts-posts') {
                return Post::resolveMenuItem($item, $url, $theme);
            }
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Dynamedia\Posts\Components\DisplayPost' => 'displayPost',
            'Dynamedia\Posts\Components\DisplayCategory' => 'displayCategory',
            'Dynamedia\Posts\Components\DisplayTag' => 'displayTag',
            'Dynamedia\Posts\Components\ListPosts' => 'listPosts',
            'Dynamedia\Posts\Components\SearchPosts' => 'searchPosts',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'dynamedia.posts.some_permission' => [
                'tab' => 'posts',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {

        return [
            'posts' => [
                'label'       => 'Posts',
                'url'         => Backend::url('dynamedia/posts/posts'),
                'icon'        => 'icon-pencil-square-o',
                'permissions' => ['dynamedia.posts.*'],
                'order'       => 500,

                'sideMenu' => [
                    'new_post' => [
                        'label'       => 'New Post',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('dynamedia/posts/posts/create'),
                        'permissions' => ['dynamedia.posts.*']
                    ],
                    'posts' => [
                        'label'       => 'Posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('dynamedia/posts/posts'),
                        'permissions' => ['dynamedia.posts.*']
                    ],
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('dynamedia/posts/categories'),
                        'permissions' => ['dynamedia.posts.*']
                    ],
                    'tags' => [
                        'label'       => 'Tags',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('dynamedia/posts/tags'),
                        'permissions' => ['dynamedia.posts.*']
                    ],
                    'settings' => [
                        'label'       => 'Settings',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('system/settings/update/dynamedia/posts/settings'),
                        'permissions' => ['dynamedia.posts.*']
                    ],
                ]
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Dynamedia Posts Settings',
                'description' => 'Manage settings for the posts.',
                'category'    => 'Posts',
                'icon'        => 'icon-cog',
                'class'       => 'Dynamedia\Posts\Models\Settings',
                'order'       => 500,
                'keywords'    => 'dynamedia posts',
                'permissions' => ['*']
            ]
        ];
    }
}

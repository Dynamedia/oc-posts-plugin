<?php namespace Dynamedia\Posts;

use Backend;
use Dynamedia\Posts\Models\Settings;
use System\Classes\PluginBase;
use App;
use Event;
use Str;
use Cms\Models\ThemeData;
use Cms\Classes\Page;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Tag;
use Backend\Models\User as BackendUserModel;
use Backend\Controllers\Users as BackendUserController;
use Dynamedia\Posts\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Dynamedia\Posts\Rules\Postslug;


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
        Validator::extend('postslug', Postslug::class);

        ThemeData::extend(function ($model) {
            $model->addJsonable('images');
        });

        BackendUserModel::extend(function($model) {
            $model->addHidden('login', 'permissions', 'is_superuser', 'role_id', 'is_activated', 'activated_at', 'created_at', 'updated_at', 'deleted_at');
            $model->hasOne['profile'] = [
                'Dynamedia\Posts\Models\Profile',
                'table' => 'dynamedia_posts_profiles'
            ];
        });

        BackendUserController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof BackendUserModel) {
                return;
            }
            if (!$model->exists) {
                return;
            }

            Profile::getFromUser($model);

            $form->addTabFields([
                'profile[username]' => [
                    'label'     => 'Username',
                    'tab'       => 'Profile',
                    'required'  => true,
                ],
                'profile[twitter_handle]' => [
                    'label' => 'Twitter Username',
                    'tab'   => 'Profile',
                    'placeholder' => "@yourUsername"
                ],
                'profile[instagram_handle]' => [
                    'label' => 'Instagram Username',
                    'tab'   => 'Profile',
                    'placeholder' => "@yourUsername"
                ],
                'profile[facebook_handle]' => [
                'label' => 'Facebook Username',
                'tab'   => 'Profile',
                'placeholder' => "yourUsername"
                ],
                'profile[website_url]' => [
                    'label' => 'Website URL',
                    'tab'   => 'Profile',
                    'placeholder' => "https://yourwebsite.com"
                ],
                'profile[mini_biography]' => [
                    'label' => 'Mini Biography',
                    'tab'   => 'Profile',
                    'type'  => 'richeditor',
                ],
                'profile[full_biography]' => [
                    'label' => 'Full Biography',
                    'tab'   => 'Biography',
                    'type'  => 'richeditor',
                    'size'  => 'huge',
                ]
            ]);
        });

        Event::listen('cms.page.beforeDisplay', function ($controller, $url, $page) {
            // Having completely semantic URLs is nice, but it causes post and category display routes to clash
            // We can deal with that by checking whether the provided slug is either a Post or a Category
            // It can't be both, as we don't allow it through validation. We will force render the relevant page.

            // Get info for potential clashing pages and the page the router actually matched
            $params = $controller->getRouter()->getParameters();

            $postPage = [
                'page' => $pg = Settings::get('postPage'),
                'url'  => Page::url($pg, $params)
            ];
            $categoryPage = [
                'page' => $pg = Settings::get('categoryPage'),
                'url'  => Page::url($pg, $params)
            ];
            $matchedPage = [
                'page' => $pg = $page->getFileNameParts()[0],
                'url' => Page::url($pg, $params)
            ];


            // Logic attempts to avoid querying both Posts and Categories if at all possible
            // But no way to avoid if the post and category pages do clash

            // Post Page
            if ($matchedPage['url'] == $postPage['url']) {
                $post = Post::getPost(['optionsSlug' => $this->extractSlug($controller)]);
                App::instance('dynamedia.posts.post', $post);
            }

            // Category Page
            if ($matchedPage['url'] == $categoryPage['url']) {
                $category = Category::getCategory(['optionsSlug' => $this->extractSlug($controller)]);
                App::instance('dynamedia.posts.category', $category);
            }

            if (!empty($post)) return $controller->render($postPage['page'], $params);
            if (!empty($category)) return $controller->render($categoryPage['page'], $params);

            // Tags can't clash with Posts and Tags so can share the same name
            if ($matchedPage == Settings::get('tagPage')) {
                $tag = Tag::getTag(['optionsSlug' => $this->extractSlug($controller)]);
                App::instance('dynamedia.posts.tag', $tag);
            }
        });

        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'posts-category'       => 'Posts: A Category',
                'posts-all-categories' => 'Posts: All Categories',
                'posts-tag'            => 'Posts: A Tag',
                'posts-all-tags'       => 'Posts: All Tags',
                'posts-post'           => 'Posts: A Post',
                'posts-all-posts'      => 'Posts: All Posts',
                'posts-category-posts' => 'Posts: All Posts From Category',
                'posts-tag-posts'      => 'Posts: All Posts With Tag',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'posts-category' || $type == 'posts-all-categories') {
                return Category::getMenuTypeInfo($type);
            } elseif ($type == 'posts-tag' || $type == 'posts-all-tags') {
                return Tag::getMenuTypeInfo($type);
            }
            elseif ($type == 'posts-post' || $type == 'posts-all-posts' || $type == 'posts-category-posts' || $type == 'posts-tag-posts') {
                return Post::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'posts-category' || $type == 'posts-all-categories') {
                return Category::resolveMenuItem($item, $url, $theme);
            }
            if ($type == 'posts-tag' || $type == 'posts-all-tags') {
                return Tag::resolveMenuItem($item, $url, $theme);
            }
            elseif ($type == 'posts-post' || $type == 'posts-all-posts' || $type == 'posts-category-posts' || $type == 'posts-tag-posts') {
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
            'Dynamedia\Posts\Components\DisplayUser' => 'displayUser',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        // Publishers and developers have full access. Some restricted non-system roles are created for more control
        return [
            'dynamedia.posts.access_plugin' => [
                'tab' => 'Posts',
                'label' => 'Access Posts Plugin',
                'order' => 1000,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.create_posts' => [
                'tab' => 'Posts',
                'label' => 'Create Posts',
                'order' => 1010,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.categorize_posts' => [
                'tab' => 'Posts',
                'label' => 'Categorize Posts',
                'order' => 1020,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.tag_posts' => [
                'tab' => 'Posts',
                'label' => 'Tag Posts',
                'order' => 1030,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.set_layout' => [
                'tab' => 'Posts',
                'label' => 'Set Post Layout',
                'order' => 1040,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish Own Posts',
                'order' => 1050,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish Own Posts',
                'order' => 1060,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit Own Published Posts',
                'order' => 1070,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Unpublished Posts',
                'order' => 1080,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Published Posts',
                'order' => 1090,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish All Posts',
                'order' => 1100,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish All Posts',
                'order' => 1110,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Unpublished Posts',
                'order' => 1120,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Published Posts',
                'order' => 1130,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Unpublished Posts',
                'order' => 1140,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Published Posts',
                'order' => 1150,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.assign_posts' => [
                'tab' => 'Posts',
                'label' => 'Assign Post to User',
                'order' => 1160,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_categories' => [
                'tab' => 'Posts',
                'label' => 'View Categories',
                'order' => 1170,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_categories' => [
                'tab' => 'Posts',
                'label' => 'Manage Categories',
                'order' => 1180,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_tags' => [
                'tab' => 'Posts',
                'label' => 'View Tags',
                'order' => 1190,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_tags' => [
                'tab' => 'Posts',
                'label' => 'Manage Tags',
                'order' => 1200,
                'roles' => ['publisher', 'developer']
            ],
             'dynamedia.posts.view_settings' => [
                'tab' => 'Posts',
                'label' => 'View Settings',
                 'order' => 1210,
                 'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_settings' => [
                'tab' => 'Posts',
                'label' => 'Manage Settings',
                'order' => 1220,
                'roles' => ['publisher', 'developer']
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
                'permissions' => ['dynamedia.posts.access_plugin'],
                'order'       => 500,
                'sideMenu' => [
                    'new_post' => [
                        'label'       => 'New Post',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('dynamedia/posts/posts/create'),
                        'permissions' => ['dynamedia.posts.create_posts']
                    ],
                    'posts' => [
                        'label'       => 'Posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('dynamedia/posts/posts'),
                        'permissions' => ['dynamedia.posts.access_plugin']
                    ],
                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-list-ol',
                        'url'         => Backend::url('dynamedia/posts/categories'),
                        'permissions' => ['dynamedia.posts.view_categories']
                    ],
                    'tags' => [
                        'label'       => 'Tags',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('dynamedia/posts/tags'),
                        'permissions' => ['dynamedia.posts.view_tags']
                    ],
                    'settings' => [
                        'label'       => 'Settings',
                        'icon'        => 'icon-cog',
                        'url'         => Backend::url('system/settings/update/dynamedia/posts/settings'),
                        'permissions' => ['dynamedia.posts.view_settings']
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
                'permissions' => ['dynamedia.posts.view_settings']
            ]
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'extractRepeaterData' => [$this, 'extractRepeaterDataFunction'],
            ],
            'filters' => [
                'modelToArray' => [$this, 'modelToArrayFilter'],
                'extractUrlParam' => [$this, 'extractUrlParamFilter'],
            ],
        ];
    }

    /*
     * Takes repeater data and converts to key/value array
     */
    public function extractRepeaterDataFunction($data)
    {
        $keyVal = [];
        if (is_array($data)) {
            foreach ($data as $item) {
                $keyVal[$item['key']] = $item['value'];
            }
        }
        return $keyVal;
    }

    /*
     * Extracts the given paramater from an url
     */
    public function extractUrlParamFilter($data, $param)
    {
        $query = !empty(parse_url($data)['query']) ? parse_url($data)['query'] : false;
        if ($query) {
            parse_str($query, $params);
        }
        return !empty($params[$param]) ? $params[$param] : null;
    }


    public function modelToArrayFilter($data)
    {
        return $data->toArray();
    }

    /**
     * Check for a relevant url parameter
     * @param $controller
     * @return string|null
     */
    private function extractSlug($controller)
    {
        $slug = null;

        if ($controller->param('postsPostSlug')) {
            $slug = $controller->param('postsPostSlug');
        } elseif ($controller->param('postsCategorySlug')) {
            $slug = $controller->param('postsCategorySlug');
        } elseif ($controller->param('postsFullPath')) {
            $slug = basename($controller->param('postsFullPath'));
        } elseif ($controller->param('postsCategoryPath')) {
            $slug = basename($controller->param('postsCategoryPath'));
        } elseif ($controller->param('postsTagSlug')) {
            $slug = $controller->param('postsTagSlug');
        }

        return $slug;
    }

    private function getComponents($page, $type)
    {
        if (!empty($page->settings['components'])) {

            return collect($page->settings['components'])
                ->filter(function ($v, $k) use ($type) {
                    if (Str::startsWith($k, $type)) return true;
            });
        }
    }
}

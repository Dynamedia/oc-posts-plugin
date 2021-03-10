<?php namespace Dynamedia\Posts;

use Backend;
use Dynamedia\Posts\Controllers\Posts;
use System\Classes\PluginBase;
use Lang;
use App;
use Event;
use Str;
use Cms\Models\ThemeData;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Tag;
use Backend\Models\User as BackendUserModel;
use Backend\Controllers\Users as BackendUserController;
use Dynamedia\Posts\Models\Profile;


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
        ThemeData::extend(function ($model) {
            $model->addJsonable('images');
        });

        BackendUserModel::extend(function($model) {
            $model->addHidden('id', 'login', 'permissions', 'is_superuser', 'role_id', 'is_activated', 'activated_at', 'created_at', 'updated_at', 'deleted_at');
            $model->hasOne['profile'] = ['Dynamedia\Posts\Models\Profile'];
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
                'label' => 'Twitter Username',
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

        Event::listen('backend.form.extendFields', function($widget) {
            // Only for the Posts controller
            if (!$widget->getController() instanceof Posts) {
                return;
            }
            // Extend the seo tabs depending on the selected type of post. todo lots!
            if ($widget->alias == 'formSeonestedform') {
                $widget->addTabFields([
                    'post_type' => [
                        'tab' => 'JSON+LD',
                        'label' => 'Post Type',
                        'type' => 'dropdown',
                        'options' => [
                            'article' => 'Article',
                            'blogposting' => 'Blog Posting'
                        ]
                    ],
                    // Force the refresh of the form so we can hook in
                    '_dependent' => [
                        'tab' => 'JSON+LD',
                        'label' => 'Testing',
                        'dependsOn' => 'post_type',
                    ]
                ]);
            }
        });



        Event::listen('cms.page.beforeDisplay', function ($controller, $url, $page) {

            $displayCategory = $this->getComponents($page, "displayCategory");
            $displayPost = $this->getComponents($page, "displayPost");
            $displayTag = $this->getComponents($page, "displayTag");
            $slug = $this->extractSlug($controller);

            if (!$slug) return;

            if ($displayCategory) {
                $category = Category::getCategoryAsArray(['optionsSlug' => $slug]);

                if ($category && $category['computed_cms_layout'] !== false) {
                    $page->layout = $category['computed_cms_layout'];
                }

                App::instance('dynamedia.posts.category', $category);
            }

            if ($displayPost) {
                $post = Post::getPostAsArray(['optionsSlug' => $slug]);

                if ($post && $post['computed_cms_layout'] !== false) {
                    $page->layout = $post['computed_cms_layout'];
                }

                App::instance('dynamedia.posts.post', $post);
            }

            if ($displayTag) {
                $tag = Tag::getTagAsArray($slug);

                if ($tag && $tag['computed_cms_layout'] !== false) {
                    $page->layout = $tag['computed_cms_layout'];
                }

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
        return [
            'dynamedia.posts.access_plugin' => [
                'tab' => 'Posts',
                'label' => 'Access Posts Plugin',
                'order' => 1000
            ],
            'dynamedia.posts.create_posts' => [
                'tab' => 'Posts',
                'label' => 'Create Posts',
                'order' => 1010
            ],
            'dynamedia.posts.categorize_posts' => [
                'tab' => 'Posts',
                'label' => 'Categorize Posts'
                ,
                'order' => 1020
            ],
            'dynamedia.posts.tag_posts' => [
                'tab' => 'Posts',
                'label' => 'Tag Posts',
                'order' => 1030
            ],
            'dynamedia.posts.set_layout' => [
                'tab' => 'Posts',
                'label' => 'Set Post Layout',
                'order' => 1040
            ],
            'dynamedia.posts.publish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish Own Posts',
                'order' => 1050
            ],
            'dynamedia.posts.unpublish_own_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish Own Posts',
                'order' => 1060
            ],
            'dynamedia.posts.edit_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit Own Published Posts',
                'order' => 1070
            ],
            'dynamedia.posts.delete_own_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Unpublished Posts',
                'order' => 1080
            ],
            'dynamedia.posts.delete_own_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete Own Published Posts',
                'order' => 1090
            ],
            'dynamedia.posts.publish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Publish All Posts',
                'order' => 1100
            ],
            'dynamedia.posts.unpublish_all_posts' => [
                'tab' => 'Posts',
                'label' => 'Unpublish All Posts',
                'order' => 1110
            ],
            'dynamedia.posts.edit_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Unpublished Posts',
                'order' => 1120
            ],
            'dynamedia.posts.edit_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Edit All Published Posts',
                'order' => 1130
            ],
            'dynamedia.posts.delete_all_unpublished_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Unpublished Posts',
                'order' => 1140
            ],
            'dynamedia.posts.delete_all_published_posts' => [
                'tab' => 'Posts',
                'label' => 'Delete All Published Posts',
                'order' => 1150
            ],
            'dynamedia.posts.assign_posts' => [
                'tab' => 'Posts',
                'label' => 'Assign Post to User',
                'order' => 1160
            ],
            'dynamedia.posts.view_categories' => [
                'tab' => 'Posts',
                'label' => 'View Categories',
                'order' => 1170
            ],
            'dynamedia.posts.manage_categories' => [
                'tab' => 'Posts',
                'label' => 'Manage Categories',
                'order' => 1180
            ],
            'dynamedia.posts.view_tags' => [
                'tab' => 'Posts',
                'label' => 'View Tags',
                'order' => 1190
            ],
            'dynamedia.posts.manage_tags' => [
                'tab' => 'Posts',
                'label' => 'Manage Tags',
                'order' => 1200
            ],
             'dynamedia.posts.view_settings' => [
                'tab' => 'Posts',
                'label' => 'View Settings',
                 'order' => 1210
            ],
            'dynamedia.posts.manage_settings' => [
                'tab' => 'Posts',
                'label' => 'Manage Settings',
                'order' => 1220
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

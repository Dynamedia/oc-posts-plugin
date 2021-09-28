<?php namespace Dynamedia\Posts;

use Backend;
use Dynamedia\Posts\Classes\Extenders\ExtendStaticPages;
use Dynamedia\Posts\Classes\Extenders\ExtendThemeFormConfig;
use Dynamedia\Posts\Classes\Listeners\CategoryTranslationModel;
use Dynamedia\Posts\Classes\Listeners\PostsController;
use Dynamedia\Posts\Classes\Listeners\PostsRouteDetection;
use Dynamedia\Posts\Classes\Acl\AccessControl;
use Dynamedia\Posts\Classes\Extenders\ExtendBackendUser;
use Dynamedia\Posts\Classes\Listeners\PostModel;
use Dynamedia\Posts\Classes\Listeners\CategoryModel;
use Dynamedia\Posts\Classes\Listeners\PostTranslationModel;
use Dynamedia\Posts\Classes\Listeners\TagModel;
use Dynamedia\Posts\Classes\Listeners\AccessControl as AccessControlListener;
use Dynamedia\Posts\Classes\Listeners\StaticPagesMenu;
use Dynamedia\Posts\Classes\Listeners\TagTranslationModel;
use Dynamedia\Posts\Classes\Seo\Schema\ExtendedGraph;
use Dynamedia\Posts\Classes\Twig\TwigFilters;
use Dynamedia\Posts\Classes\Twig\TwigFunctions;
use System\Classes\PluginBase;
use Event;
use Dynamedia\Posts\Classes\Seo\Schema\SchemaFactory;
use App;


/**
 * posts Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.Translate'
    ];
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


    public function boot()
    {
        $this->registerEvents();
        $this->registerExtenders();
        // Bind a graph object to build on
        App::instance('dynamedia.posts.graph', new ExtendedGraph());
    }

    public function registerComponents()
    {
        return [
            'Dynamedia\Posts\Components\DisplayPost' => 'displayPost',
            'Dynamedia\Posts\Components\DisplayCategory' => 'displayCategory',
            'Dynamedia\Posts\Components\DisplayTag' => 'displayTag',
            'Dynamedia\Posts\Components\ListPosts' => 'listPosts',
            'Dynamedia\Posts\Components\SearchPosts' => 'searchPosts',
            'Dynamedia\Posts\Components\DisplayUser' => 'displayUser',
            'Dynamedia\Posts\Components\MainSitemap' => 'mainSitemap',
            'Dynamedia\Posts\Components\MainRss' => 'mainRss',
        ];
    }

    public function registerPermissions()
    {
        return AccessControl::getAvailablePermissions();
    }

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
                    'aboutposts' => [
                        'label'       => 'About Posts',
                        'icon'        => 'icon-info',
                        'url'         => Backend::url('dynamedia/posts/aboutposts'),
                        'permissions' => ['dynamedia.posts.access_plugin']
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
            'functions' => TwigFunctions::getFunctions(),
            'filters' => TwigFilters::getFilters(),
        ];
    }

    public function registerEvents()
    {
        Event::subscribe(PostsRouteDetection::class);
        Event::subscribe(AccessControlListener::class);
        Event::subscribe(StaticPagesMenu::class);
        Event::subscribe(PostModel::class);
        Event::subscribe(PostsController::class);
        Event::subscribe(CategoryModel::class);
        Event::subscribe(TagModel::class);
        Event::subscribe(PostTranslationModel::class);
        Event::subscribe(CategoryTranslationModel::class);
        Event::subscribe(TagTranslationModel::class);
    }

    public function registerExtenders()
    {
        Event::subscribe(ExtendBackendUser::class);
        Event::subscribe(ExtendThemeFormConfig::class);
        Event::subscribe(ExtendStaticPages::class);
    }
}

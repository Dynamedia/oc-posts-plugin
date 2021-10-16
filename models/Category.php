<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use Dynamedia\Posts\Classes\Seo\CategorySeoParser;
use Model;
use BackendAuth;
use October\Rain\Database\Traits\NestedTree;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use October\Rain\Database\Traits\Validation;
use Dynamedia\Posts\Traits\TranslatableContentObjectTrait;
use Event;
use Cache;
use RainLab\Translate\Classes\Translator;

/**
 * category Model
 */
class Category extends Model
{
    use SeoTrait;
    use ImagesTrait;
    use ControllerTrait;
    use Validation;
    use NestedTree;
    use TranslatableContentObjectTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dynamedia_posts_categories';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
    ];

    public $customMessages = [
        'required' => 'The :attribute field is required.',
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
        'body_document',
        'images',
        'seo',
        'post_list_options',
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
        'url',
        'seo_search_title',
        'seo_search_description',
        'seo_opengraph_title',
        'seo_opengraph_description',
        'seo_opengraph_image',
        'seo_twitter_title',
        'seo_twitter_description',
        'seo_twitter_image',
        'post_list_ids',
        'post_list_sort',
        'post_list_per_page',
        'computed_cms_layout',
        'path_from_root',
        'path_to_root',
        'subcategory_ids',
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'post_list_options',
        'parent_id',
        'nest_left',
        'nest_right',
        'nest_depth',
        'cms_layout',
        'seo',
    ];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'categoryslugs' => [
            'Dynamedia\Posts\Models\CategorySlug',
            'key' => 'category_id',
        ],
        'translations' => [
            'Dynamedia\Posts\Models\CategoryTranslation',
            'key' => 'native_id'
        ]
    ];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'posts' => ['Dynamedia\Posts\Models\Post',
            'table' => 'dynamedia_posts_posts_categories',
            'order' => 'title',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // ------------------------- //
    // ---- Events Handling ---- //
    // ------------------------- //

    // todo move this into a custom validation rule
    public function beforeValidate()
    {
        Event::fire('dynamedia.posts.category.validating', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.category.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterSave()
    {
        Event::fire('dynamedia.posts.category.saved', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.category.deleting', [$this, $user = BackendAuth::getUser()]);
    }
    public function afterDelete()
    {
        Event::fire('dynamedia.posts.category.deleted', [$this, $user = BackendAuth::getUser()]);
    }



    // ---------------------- //
    // ---- Query Scopes ---- //
    // ---------------------- //

    // todo delete?
    public function scopeApplyHasPost($query, $post)
    {
        return $query->whereHas('posts', function ($p) use ($post) {

        });
    }

    public function scopeApplyWithChildren($query)
    {
        return $query->with([
            'children'
        ]);
    }


    // -------------------- //
    // ---- Posts List ---- //
    // -------------------- //

    /**
     * Get the ordering string for associated posts
     *
     * @return string
     */
    public function getPostsListSortOrder()
    {
        if (!empty($this->post_list_options['sort_order']) && $this->post_list_options['sort_order'] != '__inherit__' ) {
            $sort = $this->post_list_options['sort_order'];
        } else {
            $sort = Settings::instance()->get('categoryPostsListSortOrder');
        }

        if (!$sort) {
            $sort = 'published_at desc';
        }

        return $sort;
    }

    private function getPostsListIncludeSubcategories()
    {
        if (!empty($this->post_list_options['include_subcategories']) && $this->post_list_options['include_subcategories'] != '__inherit__' ) {
            $value = $this->post_list_options['include_subcategories'];
        } else {
            $value = Settings::instance()->get('categoryPostsListIncludeSubcategories');
        }

        return $value;
    }

    private function getPostListIds()
    {
        if ($this->getPostsListIncludeSubcategories()) {
            return $this->getAllChildrenAndSelf()->lists('id');
        } else {
            return [$this->id];
        }
    }

    private function getPostsListPerPage()
    {
        if (!empty($this->post_list_options['per_page']) && $this->post_list_options['per_page'] != '__inherit__' ) {
            $value = $this->post_list_options['per_page'];
        } else {
            $value = Settings::instance()->get('categoryPostsListPerPage');
        }

        return $value;
    }

    /**
     * Get a single category as an array
     *
     * todo move to a tag repository
     *
     * @param $options
     * @return array
     */
    public static function getCategory($options)
    {
        $optionsSlug            = false;
        $optionsWithChildren    = false;

        extract($options);

        // Look for a category which uses, or has used this slug
        // All category slugs (current, historical, translations and translation historical) exist as CategorySlugs

        $categoryId = CategorySlug::where('slug', $optionsSlug)->pluck('category_id')->toArray();

        if (!$optionsSlug || !$categoryId) return null;

        $query = Category::whereIn('id', $categoryId);

        if ($optionsWithChildren) {
            $query->applyWithChildren();
        }

        $result = $query->first();

        return $result ? $result : null;
    }



    // --------------------- ------- //
    // ---- Helpers and Getters ---- //
    // ---------------------- ------ //

    public function getCmsLayout()
    {
        if ($this->cms_layout == "__inherit__" && Settings::instance()->get('defaultCategoryLayout') == '__inherit__') {
            // No modifier
            return false;
        }
        elseif ($this->cms_layout == '__inherit__') {
            return Settings::instance()->get('defaultCategoryLayout');
        }
        else {
            return $this->cms_layout;
        }
    }

    public function getPathFromRoot($locale = null)
    {
        // Never cache longer than the request.
        $cacheKey = self::class . "_{$this->id}_path_from_root_" . $locale;
        if (Cache::store('array')
            ->has($cacheKey)) return Cache::store('array')->get($cacheKey);

        $path = [];

        foreach ($this->getParents() as $node) {
            $values = [];
            $values['id'] = $node->id;
            $values['name'] = $node->getTranslated('name', $node->attributes['name'], $locale, true);
            $values['slug'] = $node->getTranslated('slug', $node->attributes['slug'], $locale, true);

            $path[] = $values;
        }

        $self = [
            'id' => $this->id,
            'name' => $this->getTranslated('name', $this->attributes['name'], $locale, true),
            'slug' => $this->getTranslated('slug', $this->attributes['slug'], $locale, true)
        ];

        $path[] = $self;

        \Cache::store('array')->put($cacheKey, $path, 5);
        return $path;
    }

    public function getPathToRoot($locale = null)
    {
        $path = array_reverse($this->getPathFromRoot($locale));
        return $path;
    }

    public function getCachedPathFromRoot($locale = null)
    {
        // Can cache this because if urls are stale we get redirects
        $cacheKey = self::class . "_{$this->id}_cached_path_from_root_" . $locale;
        if (\Cache::has($cacheKey)) return \Cache::get($cacheKey);

        $path = $this->getPathFromRoot();
        foreach ($path as &$item) {
            $item['url'] = self::find($item['id'])->url;
        }
        //\Cache::put($cacheKey, $path, 3600);
        return $path;
    }


    // ------------------------------------------- //
    // ---- Attributes for API Representation ---- //
    // ------------------------------------------- //

    /**
     * Sets the "url" attribute with a URL to this object relative to the current locale
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->getUrlInLocale(Translator::instance()->getLocale());
    }

    /**
     * Get the url of the category according to the specified locale code
     *
     * @param null $locale
     * @return string
     */
    public function getUrlInLocale($locale = null)
    {
        if (!$locale) $locale = Translator::instance()->getLocale();

        // Per request caching - todo consider longer cache but needs clever invalidation (category structure) - also refactor w/helper
        $cacheKey = self::class . "_{$this->id}_url_in_locale_" . $locale;
        if (Cache::store('array')->has($cacheKey)) {
            return Cache::store('array')
                ->get($cacheKey);
        }

        $slug = $this->getTranslated('slug', $this->attributes['slug'], $locale, true);

        $path = implode('/', array_map(function ($entry) {
            return $entry['slug'];
        }, $this->getPathFromRoot($locale)));

        $params = [
            'postsCategoryPath' => $path,
            'postsFullPath' => $path,
            'postsCategorySlug' => $slug
        ];

        // Avoid issue where category page settings have not been completed
        $categoryPage = Settings::instance()->get('categoryPage');
        if ($categoryPage) {
            $defaultUrl = strtolower($this->getController()->pageUrl($categoryPage, $params));
        } else {
            $defaultUrl = "/";
        }

        $parts = parse_url($defaultUrl);
        $path = array_get($parts, 'path');

        $translatedUrl = http_build_url($parts, [
            'path' => '/' . Translator::instance()->getPathInLocale($path, $locale)
        ]);

        Cache::store('array')
            ->put($cacheKey, $translatedUrl);

        return $translatedUrl;
    }

    public function getPostListIdsAttribute()
    {
        return $this->getPostListIds();
    }

    public function getPostListSortAttribute()
    {
        return $this->getPostsListSortOrder();
    }

    public function getPostListPerPageAttribute()
    {
        return $this->getPostsListPerPage();
    }

    public function getComputedCmsLayoutAttribute()
    {
        return $this->getCmsLayout();
    }

    public function getPathFromRootAttribute()
    {
        return $this->getPathFromRoot();
    }

    public function getPathToRootAttribute()
    {
        return $this->getPathToRoot();
    }

    public function getSubcategoryIdsAttribute()
    {
        return $this->getAllChildren()->lists('id');
    }

    public function getPublishedPostsCountAttribute()
    {
        return $this->posts()->applyIsPublished()->count();
    }

    public function getUnpublishedPostsCountAttribute()
    {
        return $this->posts()->applyIsNotPublished()->count();
    }


    public function filterFields($fields, $context = null)
    {
        $user = BackendAuth::getUser();

        // Body Type
        if (isset($fields->body_type)) {

            if ($fields->body_type->value == 'repeater_body') {
                $fields->repeater_body->hidden = false;
                $fields->richeditor_body->hidden = true;
                $fields->markdown_body->hidden = true;
            }
            elseif ($fields->body_type->value == 'richeditor_body') {
                $fields->repeater_body->hidden = true;
                $fields->richeditor_body->hidden = false;
                $fields->markdown_body->hidden = true;

            }
            elseif ($fields->body_type->value == 'markdown_body') {
                $fields->repeater_body->hidden = true;
                $fields->richeditor_body->hidden = true;
                $fields->markdown_body->hidden = false;
            }
            else {
                $fields->repeater_body->hidden = false;
                $fields->richeditor_body->hidden = true;
                $fields->markdown_body->hidden = true;
            }
        }
        if (isset($fields->type)) {
            $fields->type->hidden = true;
        }
        if (isset($fields->about)) {
            $fields->about->hidden = true;
        }

        if (!AccessControl::userCanManageTranslations($user)) {
            if (isset($fields->translations)) {
                $fields->translations->comment = "You do not have permission to manage translations";
            }
        }

        if (!AccessControl::userCanManageSlugs($user)) {
            if (isset($fields->categoryslugs)) {
                $fields->categoryslugs->comment = "You do not have permission to manage related slugs";
            }
        }
    }


    // ---------------------------- //
    // ---- Rainlab Pages Menu ---- //
    // ---------------------------- //

    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     * Returns a menu item type information. The type information is returned as array
     * with the following elements:
     * - references - a list of the item type reference options. The options are returned in the
     *   ["key"] => "title" format for options that don't have sub-options, and in the format
     *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
     *   required only if the menu item type requires references.
     * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
     *   false if omitted.
     * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
     *   Optional, false if omitted.
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
     *   resolve the item URL.
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'posts-category') {
            $result = [
                'references'   => self::listSubCategoryOptions(),
                'nesting'      => true,
                'dynamicItems' => true
            ];
        }

        if ($type == 'posts-all-categories') {
            $result = [
                'dynamicItems' => true
            ];
        }

        return $result;
    }

    protected static function listSubCategoryOptions()
    {
        $category = self::getNested();

        $iterator = function($categories) use (&$iterator) {
            $result = [];

            foreach ($categories as $category) {
                if (!$category->children) {
                    $result[$category->id] = $category->name;
                }
                else {
                    $result[$category->id] = [
                        'title' => $category->name,
                        'items' => $iterator($category->children)
                    ];
                }
            }

            return $result;
        };

        return $iterator($category);
    }

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * Returns information about a menu item. The result is an array
     * with the following keys:
     * - url - the menu item URL. Not required for menu item types that return all available records.
     *   The URL should be returned relative to the website root and include the subdirectory, if any.
     *   Use the Url::to() helper to generate the URLs.
     * - isActive - determines whether the menu item is active. Not required for menu item types that
     *   return all available records.
     * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
     *   The items array should be added only if the $item's $nesting property value is TRUE.
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'posts-category') {
            if (!$item->reference) {
                return;
            }

            $category = self::find($item->reference);
            if (!$category) {
                return;
            }

            $result = [];
            $result['url'] = $category->url;
            $result['isActive'] = $category->url == $url;
            $result['mtime'] = $category->updated_at;

            if ($item->nesting) {
                $categories = $category->getNested();
                $iterator = function($categories) use (&$iterator, &$item, &$theme, $url) {
                    $branch = [];

                    foreach ($categories as $category) {

                        $branchItem = [];
                        $branchItem['url'] = $category->url;
                        $branchItem['isActive'] = $branchItem['url'] == $url;
                        $branchItem['title'] = $category->name;
                        $branchItem['mtime'] = $category->updated_at;

                        if ($category->children) {
                            $branchItem['items'] = $iterator($category->children);
                        }

                        $branch[] = $branchItem;
                    }

                    return $branch;
                };

                $result['items'] = $iterator($categories);
            }
        }
        elseif ($item->type == 'posts-all-categories') {
            $result = [
                'items' => []
            ];

            // No sorting - Follow user sort
            $categories = self::get();
            foreach ($categories as $category) {
                $categoryItem = [
                    'title' => $category->name,
                    'url'   => $category->url,
                    'mtime' => $category->updated_at
                ];

                $categoryItem['isActive'] = $categoryItem['url'] == $url;

                $result['items'][] = $categoryItem;
            }
        }
        return $result;
    }


    /**
     * Add article data to the global schema graph object
     */
    public function setSeo() {
        $seoParser = (new CategorySeoParser($this))
            ->setProperties();
    }


}

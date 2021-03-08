<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Settings;
use Model;
use Str;
use Input;
use Config;
use BackendAuth;
use ValidationException;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use \October\Rain\Database\Traits\Validation;
use Cache;


/**
 * tag Model
 */
class Tag extends Model
{

    use SeoTrait, ImagesTrait, ControllerTrait, Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dynamedia_posts_tags';

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
        'slug' => 'unique:dynamedia_posts_tags',
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
        'body',
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
        'post_list_sort',
        'post_list_per_page',
        'computed_cms_layout',
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'post_list_options',
        'cms_layout'
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
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'posts' => ['Dynamedia\Posts\Models\Post',
            'table' => 'dynamedia_posts_posts_tags',
            'order' => 'title',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // ------------------------ //
    // ---- Event Handling ---- //
    // ------------------------ //

    // For tag widget
    public function beforeSave()
    {
        $user = BackendAuth::getUser();

        // Allow creation (from posts tag interface)
        if (!$this->userCanManage($user) && !$this->exists) {
            $this->is_approved = false;
        } elseif (!$this->userCanManage($user)) {
            throw new ValidationException([
                'error' => "Insufficient permissions to edit {$this->name}"
            ]);
        }

        if (!$this->slug) {
            $this->slug = Str::slug($this->name);
        }

        $this->slug = Str::slug($this->slug);
    }

    public function afterDelete()
    {
        $this->posts()->detach();
    }



    // ---------------------- //
    // ---- Query Scopes ---- //
    // ---------------------- //

    /**
     * Only approved tags
     *
     * @param $query
     * @return mixed
     */
    public function scopeApplyIsApproved($query)
    {
        return $query->where('is_approved', true);
    }



    // --------------------- //
    // ---- Form Widget ---- //
    // --------------------- //

    public function filterFields($fields, $context = null)
    {
        // Hide fields if user is here but not permitted to view
        if (!$this->userCanView(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->hidden = true;
            }
        }
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
            $sort = Settings::get('tagPostsListSortOrder');
        }

        if (!$sort) {
            $sort = 'published_at desc';
        }

        return $sort;
    }

    public function getPostsListPerPage()
    {
        if (!empty($this->post_list_options['per_page']) && $this->post_list_options['per_page'] != '__inherit__' ) {
            $value = $this->post_list_options['per_page'];
        } else {
            $value = Settings::get('tagPostsListPerPage');
        }

        return $value;
    }


    // --------------------- ------- //
    // ---- Helpers and Getters ---- //
    // ---------------------- ------ //

    public function getCmsLayout()
    {
        if ($this->cms_layout == "__inherit__" && Settings::get('defaultTagLayout') == '__inherit__') {
            // No modifier
            return false;
        }
        elseif ($this->cms_layout == '__inherit__') {
            return Settings::get('defaultTagLayout');
        }
        else {
            return $this->cms_layout;
        }
    }

    /**
     * Get a single tag as an array
     *
     * todo move to a tag repository
     *
     * @param $options
     * @return array
     */
    public static function getTagAsArray($slug)
    {
        $cacheKey = md5(__METHOD__ . "tag_{$slug}");
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = static::where('slug', $slug);

        $result = $query->first();

        if ($result) {
            $result = $result->toArray();
            Cache::put($cacheKey, $result, 10);
            return $result;
        } else {
            return [];
        }
    }



    // ------------------------------ //
    // ---- Permissions Checking ---- //
    // ------------------------------ //

    /**
     * Check if user has required permissions to view tags
     * @param $user
     * @return bool
     */
    public function userCanView($user)
    {
        if (!$user->hasAccess('dynamedia.posts.view_tags')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage tags
     * @param $user
     * @return bool
     */
    public function userCanManage($user)
    {
        if (!$user->hasAccess('dynamedia.posts.manage_tags')) {
            return false;
        } else {
            return true;
        }
    }



    // ------------------------------------------- //
    // ---- Attributes for API Representation ---- //
    // ------------------------------------------- //

    /**
     * Sets the "url" attribute with a URL to this object.
     *
     * @param Controller $controller
     * @param array $params Override request URL parameters
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $pageName = Settings::get('tagPage');

        $params = ['postsTagSlug' => $this->slug];

        return strtolower($this->getController()->pageUrl($pageName, $params));
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
     *
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'posts-tag') {
            $references = [];

            $tags = self::orderBy('name')->get();
            foreach ($tags as $tag) {
                $references[$tag->id] = $tag->name;
            }

            $result = [
                'references'   => $references,
                'nesting'      => false,
                'dynamicItems' => false
            ];
        }

        if ($type == 'posts-all-tags') {
            $result = [
                'dynamicItems' => true
            ];
        }

        return $result;
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
     *
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'posts-tag') {
            if (!$item->reference) {
                return;
            }
            $tag = self::find($item->reference);

            if (!$tag) return;

            $result = [];
            $result['url'] = $tag->url;
            $result['isActive'] = $tag->url == $url;
            $result['mtime'] = $tag->updated_at;
        }

        elseif ($item->type == 'posts-all-tags') {
            $result = [
                'items' => []
            ];

            $tags = self::orderBy('name', 'ASC')
                ->get();

            foreach ($tags as $tag) {
                $tagItem = [
                    'title' => $tag->name,
                    'url'   => $tag->url,
                    'mtime' => $tag->updated_at
                ];

                $tagItem['isActive'] = $tagItem['url'] == $url;

                $result['items'][] = $tagItem;
            }
        }
        return $result;
    }
}

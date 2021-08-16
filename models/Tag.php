<?php namespace Dynamedia\Posts\Models;
use Dynamedia\Posts\Classes\Acl\AccessControl;
use Model;
use Dynamedia\Posts\Traits\ControllerTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\TranslatableContentObjectTrait;
use October\Rain\Database\Traits\Validation;
use BackendAuth;
use Event;

/**
 * tag Model
 */
class Tag extends Model
{
    use SeoTrait;
    use ImagesTrait;
    use ControllerTrait;
    use Validation;
    use TranslatableContentObjectTrait;

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
    protected $fillable = [
        'name'
    ];

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
    public $hasMany = [
        'tagslugs' => [
            'Dynamedia\Posts\Models\TagSlug',
            'key' => 'tag_id',
        ],
        'translations' => [
            'Dynamedia\Posts\Models\TagTranslation',
            'key' => 'native_id'
        ]
    ];
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

    public function beforeValidate()
    {
        Event::fire('dynamedia.posts.tag.validating', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.tag.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterSave()
    {
        Event::fire('dynamedia.posts.tag.saved', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.tag.deleting', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterDelete()
    {
        Event::fire('dynamedia.posts.tag.deleted', [$this, $user = BackendAuth::getUser()]);
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
        if (!AccessControl::userCanViewTags(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->hidden = true;
            }
        }

        if (isset($fields->type)) {
            $fields->type->hidden = true;
        }
        if (isset($fields->about)) {
            $fields->about->hidden = true;
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
    public static function getTag($slug)
    {
        // Look for a tag which uses, or has used this slug
        // All tag slugs (current, historical, translations and translation historical) exist as TagSlugs

        $tagId = TagSlug::where('slug', $slug)->pluck('tag_id')->toArray();

        if (!$tagId) return null;

        $query = Tag::whereIn('id', $tagId);

        $query->applyIsApproved();

        $result = $query->first();

        return $result ? $result : null;
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

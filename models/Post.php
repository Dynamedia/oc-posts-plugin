<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use Dynamedia\Posts\Classes\Body\Repeaterbody\RepeaterBody;
use Dynamedia\Posts\Models\Settings;
use RainLab\Translate\Classes\Translator;
use Model;
use Event;
use October\Rain\Argon\Argon;
use BackendAuth;
use Cms\Classes\Controller;
use Flash;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use October\Rain\Database\Traits\Validation;
use Dynamedia\Posts\Traits\TranslatableContentObjectTrait;
use RainLab\Translate\Models\Locale;
use Dynamedia\Posts\Classes\Body\Body;

/**
 * post Model
 */
class Post extends Model
{
    use SeoTrait;
    use ImagesTrait;
    use ControllerTrait;
    use Validation;
    use TranslatableContentObjectTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dynamedia_posts_posts';

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
        'title' => 'required',
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
        'seo'
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
        'url',
        'contents_list',
        'pages',
        'seo_search_title',
        'seo_search_description',
        'seo_opengraph_title',
        'seo_opengraph_description',
        'seo_opengraph_image',
        'seo_twitter_title',
        'seo_twitter_description',
        'seo_twitter_image',
        'seo_schema',
        'category_ids',
        'tag_ids',
        'computed_cms_layout',
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
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
        'postslugs' => [
            'Dynamedia\Posts\Models\PostSlug',
            'key' => 'post_id',
        ],
        'translations' => [
            'Dynamedia\Posts\Models\PostTranslation',
            'key' => 'native_id',
        ]
    ];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'author' => ['Backend\Models\User'],
        'editor' => ['Backend\Models\User'],
        'primary_category' => ['Dynamedia\Posts\Models\Category'],
        // Posts have translations, but not all posts should be available in all locales
        'locale' => Locale::class
    ];
    public $belongsToMany = [
        'categories' => [
            'Dynamedia\Posts\Models\Category',
            'table' => 'dynamedia_posts_posts_categories',
        ],
        'tags' => [
            'Dynamedia\Posts\Models\Tag',
            'table' => 'dynamedia_posts_posts_tags',
            'order' => 'name'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    // ------------------------- //
    // ---- Events Handling ---- //
    // ------------------------- //

    public function beforeValidate()
    {
        Event::fire('dynamedia.posts.post.validating', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.post.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterSave()
    {
        Event::fire('dynamedia.posts.post.saved', [$this, $user = BackendAuth::getUser()]);

    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.post.deleting', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterDelete()
    {
        Event::fire('dynamedia.posts.post.deleted', [$this, $user = BackendAuth::getUser()]);
    }



    // ---------------------- //
    // ---- Query scopes ---- //
    // ---------------------- //

    public function scopeApplyAssignedToCurrentUser($query)
    {
        return $query->where('author_id', BackendAuth::getUser()->id)
            ->orWhere('editor_id', BackendAuth::getUser()->id);
    }

    public function scopeApplyWhereAuthor($query, $filter = null)
    {
        return $query->whereHas('author', function($q) use ($filter) {
            $q->whereHas('profile', function($q) use ($filter) {
                $q->where('id', $filter);
            });
        });
    }
    public function scopeApplyWhereEditor($query, $filter = null)
    {
        return $query->whereHas('editor', function($q) use ($filter) {
            $q->whereHas('profile', function($q) use ($filter) {
                $q->where('id', $filter);
            });
        });
    }


    public function scopeApplyIsPublished($query)
    {
        return $query
            ->whereNotNull('is_published')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<', Argon::now());
    }

    public function scopeApplyIsNotPublished($query)
    {
        return $query
            ->whereNull('is_published')
            ->orWhere('is_published', false)
            ->orWhere('published_at', '>', Argon::now());
    }

    public function scopeApplyWhereHasTags($query, array $tagIds)
    {
        return $query->whereHas('tags', function($q) use ($tagIds) {
            $q->whereIn('id', $tagIds)
                ->applyIsApproved();
        });
    }

    public function scopeApplyWhereHasCategories($query, array $categoryIds)
    {
        return $query->whereHas('categories', function($q) use ($categoryIds) {
            $q->whereIn('id', $categoryIds);
        });
    }

    public function scopeApplyWithActiveTranslation($query)
    {
        return $this->query->with(['translations' => function ($q) {
           $q->whereHas('locale', function($q)  {
               $q->where('code', Translator::instance()->getLocale());
           })->first();
        }]);
    }

    /**
     * This is an EXTREMELY basic search - There is no index on any of the searched columns
     * todo Implement a fast cross-db solution. Consider full text and generated (by php) column from title, excerpt and searchable body sections
     * @param $query
     * @param string $searchString
     */
    public function scopeApplySearch($query, string $searchString)
    {
        $query->where("title", "LIKE", "%{$searchString}%")
            ->orWhere("excerpt", "LIKE", "%{$searchString}%")
            ->orWhere("body_text", "LIKE", "%{$searchString}%");
    }

    public function scopeApplyOrdering($query, string $sort)
    {
        @list($sortField, $sortDirection) = explode(' ', $sort);
        if (is_null($sortDirection)) {
            $sortDirection = "desc";
        }
        return $query->orderBy($sortField, $sortDirection);
    }

    public function scopeApplyWithTags($query)
    {
        return $query->with([
            'tags' =>function($q) {
                $q->applyIsApproved();
            }
        ]);
    }

    public function scopeApplyWithTranslations($query)
    {
        return $query->with([
            'translations.locale'
        ]);
    }

    public function scopeApplyWithPrimaryCategory($query)
    {
        return $query->with([
            'primary_category'
        ]);
    }

    public function scopeApplyWithUsers($query)
    {
        return $query->with([
            'author' => function($q) {
                $q->select('id', 'first_name', 'last_name')
                    ->with('avatar', 'profile');
            },
            'editor' => function($q) {
                $q->select('id', 'first_name', 'last_name')
                    ->with('avatar', 'profile');
            }
        ]);
    }

    public function scopeApplyWhereUsername($query, $username)
    {
        return $query->whereHas('author.profile', function ($q) use ($username) {
           $q->where('username', $username);
        });
    }

    public function scopeApplyExcludePosts($query, array $ids)
    {
        return $query->whereNotIn('id', $ids);
    }

    public function scopeApplyExcludeCategoryPosts($query, array $ids)
    {
        return $query->whereDoesntHave('categories', function ($q) use ($ids) {
            $q->whereIn('id', $ids);
        })
            ->whereNotIn('primary_category_id', $ids);
    }

    public function scopeApplyExcludeTagPosts($query, array $ids)
    {
        return $query->whereDoesntHave('tags', function ($q) use ($ids) {
            $q->whereIn('id', $ids);
        });
    }

    public function scopeApplyHasLocale($query, $locale)
    {
        // Only posts written in our chosen language or translated into it
        $query->whereHas('locale', function($q) use ($locale) {
            $q->where('code', $locale);
        })
            ->orWhereHas('translations', function ($q) use ($locale) {
                $q->whereHas('locale', function ($q) use ($locale) {
                    $q->where('code', $locale);
                });
            });
    }

    /**
     * Get a single post
     *
     * todo move to a post repository
     *
     * @param $options
     * @return array
     */
    public static function getPost($options)
    {
        $optionsSlug                = false;
        $optionsWithPrimaryCategory = true;
        $optionsWithTags            = true;
        $optionsWithUsers           = true;

        extract($options);

        // Look for a post which uses, or has used this slug
        // All post slugs (current, historical, translations and translation historical) exist as PostSlugs

        $postId = PostSlug::where('slug', $optionsSlug)->pluck('post_id')->toArray();

        if (!$optionsSlug || !$postId) return null;

        $query = Post::whereIn('id', $postId);

        $query = $query->applyHasLocale(Translator::instance()->getLocale());

        $query->applyWithTranslations();

        if ($optionsWithPrimaryCategory) {
            $query->applyWithPrimaryCategory();
        }

        if ($optionsWithTags) {
            $query->applyWithTags();
        }

        if ($optionsWithUsers) {
            $query->applyWithUsers();
        }

        $result = $query->first();

        return $result ? $result : null;
    }

    /**
     * Get a list of posts
     *
     * todo document fully + json api
     *
     * @param $options
     * @return array
     */
    public static function getPostsList($options)
    {
        // Set some defaults to be overridden by extract
        $optionsLocale          = Translator::instance()->getLocale();
        $optionsTagIds          = [];
        $optionsCategoryIds     = [];
        $optionsPostIds         = [];
        $optionsNotPostIds      = [];
        $optionsNotCategoryIds  = [];
        $optionsNotTagIds       = [];
        $optionsUsername        = null;
        $optionsPage            = 1;
        $optionsPerPage         = 10;
        $optionsLimit           = false;
        $optionsSearchQuery     = null;
        $optionsSort            = 'published_at desc';

        extract($options);

        $query = static::applyIsPublished();

        // Only posts written in our chosen language or translated into it
        $query->whereHas('locale', function($q) use ($optionsLocale) {
            $q->where('code', $optionsLocale);
        })
        ->orWhereHas('translations', function ($q) use ($optionsLocale) {
           $q->whereHas('locale', function ($q) use ($optionsLocale) {
               $q->where('code', $optionsLocale);
           });
        });

        if ($optionsNotPostIds) {
            $query->applyExcludePosts($optionsNotPostIds);
        }

        if ($optionsNotCategoryIds) {
            $query->applyExcludeCategoryPosts($optionsNotCategoryIds);
        }

        if ($optionsNotTagIds) {
            $query->applyExcludeTagPosts($optionsNotTagIds);
        }

        if ($optionsTagIds) {
            $query->applyWhereHasTags($optionsTagIds);
        }

        if ($optionsCategoryIds) {
            $query->applyWhereHasCategories($optionsCategoryIds);
        }

        if ($optionsUsername) {
            $query->applyWhereUsername($optionsUsername);
        }

        if ($optionsSearchQuery) {
            $query->applySearch($optionsSearchQuery);
        }

        // Where Post ID's are specified the sorting is done here to keep them in order
        if ($optionsPostIds) {
            $stringIds = implode(",", $optionsPostIds);
            $query->whereIn('id', $optionsPostIds);
            // SQLite does not support FIELD and will get default ordering. Sorry! todo change this behaviour
            if (\DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME) != 'sqlite') {
                $query->orderByRaw("FIELD(id, $stringIds)");
                $optionsSort = false;
            }
        }

        if ($optionsSort) {
            if ($optionsSort == '__random__') {
                $query->inRandomOrder();
            } else {
                $query->applyOrdering($optionsSort);
            }
        }

        $query->applyWithPrimaryCategory()
            ->applyWithTags()
            ->applyWithUsers()
            ->applyWithTranslations();

        // We need to do paging ourselves to support later API. Paginate in the components for now
        $totalResults = $query->count();

        // Apply limits if required
        // Do not paginate
        if ($optionsLimit && $optionsLimit <= $optionsPerPage) {
            $optionsPage = 1;
            if ($totalResults > $optionsLimit) {
                $totalResults = $optionsLimit;
            }
        }

        $totalPages = (int) ceil($totalResults / $optionsPerPage);

        $result = [
            'totalResults'      => $totalResults,
            'totalPages'        => $totalPages,
            'requestedPage'     => $optionsPage,
            'itemsPerPage'      => $optionsPerPage,
        ];

        $offset = ($optionsPage - 1) * $optionsPerPage;

        $items = $query->skip($offset)
            ->take($optionsPerPage)
            ->get();

        $result ['items'] = $items;

        return $result;
    }


    // ----------------------------- //
    // ---- Helpers and Getters ---- //
    // ----------------------------- //


    public function getCmsLayout()
    {
        if ($this->cms_layout == "__inherit__" && Settings::get('defaultPostLayout') == '__inherit__') {
            // Inherit from category first.
            if ($this->primary_category) {
                return $this->primary_category->getCmsLayout();
            } else {
                return false;
            }
        }
        if ($this->cms_layout == '__inherit__') {
            return Settings::get('defaultPostLayout');
        }
        else {
            return $this->cms_layout;
        }
    }


    /**
     * Helper methods to determine the correct CMS page to
     * pass to the router.
     *
     * @return array
     */
    private function getPostPage()
    {
        $postsPage = Settings::get('postPage');

        // Plugin not configured
        if (!$postsPage) {
            if (BackendAuth::getUser()) {
                return Flash::warning('Please set the Posts page in settings ');
            }
            return;
        }

        return $postsPage;
    }

    /**
     * Process the json column 'content' and return an array
     * of pages containing our items
     * @return array
     */
    public function getPages()
    {
        return $this->body->getPages();
    }



    // --------------------- //
    // ---- Form Widget ---- //
    // --------------------- //

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



        // Set author on create
        if (!$this->author && isset($fields->author)) {
            $fields->author->value = $user->id;
        }

        if (isset($fields->author) && !AccessControl::userCanAssignPosts($user)) {
            $fields->author->readOnly = true;
            $fields->author->comment = "You do not have permission to re-assign this post";
        }

        if (isset($fields->editor) && !AccessControl::userCanAssignPosts($user)) {
            $fields->editor->readOnly = true;
            $fields->editor->comment = "You do not have permission to re-assign this post";
        }

        if ($this->is_published) {
            if (!AccessControl::userCanUnPublishPost($this, $user)) {
                if (isset($fields->is_published)) {
                    $fields->is_published->readOnly = true;
                    $fields->is_published->comment = "You do not have permission to unpublish this post";

                }
                if (isset($fields->published_at)) {
                    $fields->published_at->readOnly = true;
                }
                if (isset($fields->published_until)) {
                    $fields->published_until->readOnly = true;
                }
            }
        } else {
            if (!AccessControl::userCanPublishPost($this, $user)) {
                if (isset($fields->is_published)) {
                    $fields->is_published->readOnly = true;
                    $fields->is_published->comment = "You do not have permission to publish this post";
                }
                if (isset($fields->published_at)) {
                    $fields->published_at->readOnly = true;
                }
                if (isset($fields->published_until)) {
                    $fields->published_until->readOnly = true;
                }
            }
        }

        if (!AccessControl::userCanCategorizePosts($user)) {
            if (isset($fields->primary_category)) {
                $fields->primary_category->comment = "You do not have permission to categorize posts";
                $fields->primary_category->readOnly = true;
            }
            if (isset($fields->categories)) {
                $fields->categories->readOnly = true;
            }
        }

        if (!AccessControl::userCanTagPosts($user)) {
            if (isset($fields->tags)) {
                $fields->tags->comment = "You do not have permission to tag posts";
                $fields->tags->readOnly = true;
            }
        }

        if (!AccessControl::userCanSetLayout($user)) {
            if (isset($fields->cms_layout)) {
                $fields->cms_layout->comment = "You do not have permission to change the layout";
                $fields->cms_layout->readOnly = true;
            }
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
        $pageName = $this->getPostPage();
        $categoryPath = null;

        if ($this->primary_category) {
            $categoryPath = implode('/', array_map(function ($entry) {
                return $entry['slug'];
            }, $this->primary_category->getPathFromRoot()));
        }

        if ($categoryPath) {
            $fullPath = "{$categoryPath}/{$this->slug}";
        } else {
            $fullPath = $this->slug;
        }

        $params = [
            'postsCategoryPath' => $categoryPath,
            'postsFullPath' => $fullPath,
            'postsPostSlug'  => $this->slug,
            'postsCategorySlug' => !empty($this->primary_category) ? $this->primary_category->slug : null
        ];

        return strtolower($this->getController()->pageUrl($pageName, $params));
    }

    public function getComputedCmsLayoutAttribute()
    {
        return $this->getCmsLayout();
    }


    public function getPagesAttribute()
    {
        return $this->getPages();
    }

    /**
     * Return an array of all Category IDs associated with this Post
     *
     * @return array
     */
    public function getCategoryIdsAttribute()
    {
        $ids = [];
        if ($this->primary_category) {
            $ids = [$this->primary_category->id];
        }
        if ($this->categories->count()) {
            $ids = array_unique(array_merge($ids, $this->categories->pluck('id')->toArray()));
        }
        return $ids;
    }

    /**
     * Return an array of all Tag IDs associated with this Post
     *
     * @return array
     */
    public function getTagIdsAttribute()
    {
        $ids = [];
        if ($this->tags->count()) {
            $ids = array_merge($ids, $this->tags()->applyIsApproved()->pluck('id')->toArray());
        }
        return $ids;
    }

    // todo tidy up and objectify
    public function getSeoSchemaAttribute()
    {
        $schema = [
            "@context"          => "https://schema.org",
            "@type"             => !empty($this->seo['type']) ? $this->seo['type'] : "Article",
            "headline"          => $this->title,
            "dateCreated"       => $this->created_at,
            "url"               => $this->url,
            "mainEntityOfPage"  => [
                "@context"  => "https://schema.org",
                "@type"     => "webPage",
                "url"       => $this->url, // There is no differentiation of pages
            ]
        ];

        if ($this->excerpt) {
            $schema['abstract'] = strip_tags($this->excerpt);
        }

        if ($this->primary_category) {
            $schema['articleSection'] = $this->primary_category->name;

        }

        if ($this->is_published && $this->published_at) {
            $schema['datePublished'] = $this->published_at;
            if ($this->updated_at > $this->published_at) {
                $schema['dateModified'] = $this->updated_at;
            }
        }

        if (!empty($this->seo['about'])) {
            $schema['about'] = [
                "@context"  => "https://schema.org/",
                "@type"     => "Thing",
                "name"      => $this->seo['about']
            ];
        }

        if (!empty($this->seo['keywords']) && is_array($this->seo['keywords'])) {
            $schema['keywords'] = implode(", ", $this->seo['keywords']);
        }

        if ($this->published_until) {
            $schema['expires'] = $this->published_until;
        }

        if (Settings::get('publisherName')) {
            $schema['publisher'] = [
                "@context" => "https://schema.org",
                "@type" => Settings::get('publisherType'),
                "name" => Settings::get('publisherName'),
                "logo"  => [
                    "@context"  => "https://schema.org",
                    "@type"     => "ImageObject",
                    'url'       => \URL::to(\System\Classes\MediaLibrary::url(Settings::get('publisherLogo'))),
                    'caption'   => Settings::get('publisherName'),
                ]
            ];
            if (Settings::get('publisherUrl')) {
                $schema['publisher']['url'] = Settings::get('publisherUrl');
            } else {
                $schema['publisher']['url'] = \URL::to('/');
            }
        }

        if ($this->getBestImage()) {
            $schema['image'] = \URL::to(\System\Classes\MediaLibrary::url($this->getBestImage()));
        }

        if (!empty($this->author->profile)) {
            $schema['author'] = $this->author->profile->seo_schema;
        }

        if (!empty($this->editor->profile)) {
            $schema['editor'] = $this->editor->profile->seo_schema;
        }

        return $schema;
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

        if ($type == 'posts-post') {
            $references = [];

            $posts = self::orderBy('title')->get();
            foreach ($posts as $post) {
                $references[$post->id] = $post->title;
            }

            $result = [
                'references'   => $references,
                'nesting'      => false,
                'dynamicItems' => false
            ];
        }

        if ($type == 'posts-all-posts') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($type == 'posts-category-posts') {
            $references = [];

            $categories = Category::orderBy('name')->get();
            foreach ($categories as $category) {
                $references[$category->id] = $category->name;
            }

            $result = [
                'references'   => $references,
                'dynamicItems' => true,
                'nesting' => true
            ];
        }

        if ($type == 'posts-tag-posts') {
            $references = [];

            $tags = Tag::orderBy('name')->get();
            foreach ($tags as $tag) {
                $references[$tag->id] = $tag->name;
            }

            $result = [
                'references'   => $references,
                'dynamicItems' => true,
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

        if ($item->type == 'posts-post') {
            if (!$item->reference) {
                return;
            }
            $post = Post::find($item->reference);

            $result = [];
            $result['url'] = $post->url;
            $result['isActive'] = $post->url == $url;
            $result['mtime'] = $post->updated_at;
        }
        elseif ($item->type == 'posts-all-posts') {
            $result = [
                'items' => []
            ];

            $posts = self::applyIsPublished()
                ->orderBy('updated_at', 'ASC')
                ->get();

            foreach ($posts as $post) {
                $postItem = [
                    'title' => $post->title,
                    'url'   => $post->url,
                    'mtime' => $post->updated_at
                ];

                $postItem['isActive'] = $postItem['url'] == $url;

                $result['items'][] = $postItem;
            }
        }

        elseif ($item->type == 'posts-category-posts') {
            if (!$item->reference) {
                return;
            }

            $category = Category::find($item->reference);
            if (!$category) {
                return;
            }

            $result = [
                'items' => []
            ];

            $query = self::applyIsPublished()
                ->orderBy('title');

            if ($item->nesting) {
                $categories = $category->getAllChildrenAndSelf()->lists('id');
            } else {
                $categories = [$category->id];
            }

            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });

            $posts = $query->get();

            foreach ($posts as $post) {
                $postItem = [
                    'title' => $post->title,
                    'url'   => $post->url,
                    'mtime' => $post->updated_at
                ];

                $postItem['isActive'] = $postItem['url'] == $url;

                $result['items'][] = $postItem;
            }
        } elseif ($item->type == 'posts-tag-posts') {

            if (!$item->reference) {
                return;
            }

            $tag = Tag::find($item->reference);
            if (!$tag) {
                return;
            }

            $result = [
                'items' => []
            ];

            $query = self::applyIsPublished()
                ->orderBy('title');

            $query->whereHas('tags', function($q) use ($tag) {
                $q->whereIn('id', [$tag->id]);
            });

            $posts = $query->get();

            foreach ($posts as $post) {
                $postItem = [
                    'title' => $post->title,
                    'url'   => $post->url,
                    'mtime' => $post->updated_at
                ];

                $postItem['isActive'] = $postItem['url'] == $url;

                $result['items'][] = $postItem;
            }
        }
        return $result;
    }

    /**
     * @return mixed body object by body_document body_type
     */
    public function getBodyAttribute()
    {
        $body = Body::getBody($this->body_document);
        return $body;
    }

    public function getContentsListAttribute()
    {
        if (!$this->show_contents) return [];

        return $this->body->getContentsList($this->url);
    }



}

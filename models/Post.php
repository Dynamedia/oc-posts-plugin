<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Models\Settings;
use Model;
use Lang;
use Config;
use October\Rain\Argon\Argon;
use Illuminate\Pagination\LengthAwarePaginator;
use Input;
use Str;
use BackendAuth;
use Cms\Classes\Controller;

/**
 * post Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;

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
        'slug' => 'required|unique:dynamedia_posts_posts|unique:dynamedia_posts_categories',
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
        'seo'
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = ['url'];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

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
    public $belongsTo = [
        'user' => ['Backend\Models\User'],
        'primary_category' => ['Dynamedia\Posts\Models\Category']
    ];
    public $belongsToMany = [
        'categories' => [
            'Dynamedia\Posts\Models\Category',
            'table' => 'dynamedia_posts_posts_categories',
            'order' => 'name'
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



    public function beforeSave()
    {
        $this->slug = Str::slug($this->slug);

        if (empty($this->user)) {
            $user = BackendAuth::getUser();
            if (!is_null($user)) {
                $this->user = $user->id;
            }
        }

        if ($this->is_published && $this->published_at == null) {
            $this->published_at = Argon::now();
        }

        if (!$this->is_published) {
            $this->published_at = null;
        }
    }

    public function afterSave()
    {
        if ($this->primary_category) {
            $this->categories()->sync([$this->primary_category->id], false);
        } else {
            if ($this->categories->count() > 0) {
                $this->primary_category = $this->categories->first();
            }
        }
    }

    public function afterDelete()
    {
        $this->categories()->detach();
        $this->tags()->detach();
    }

    /**
     * Process the json column 'content' and return an array
     * of pages containing our items
     * @return array
     */
    public function getPages()
    {
        $bodyPages = [];
        $page = [];
        $curPage = 1;

        foreach ($this->body as $item) {
            // Commit the items to a page when we find a page break
            if ($item['_group'] == 'pagebreak') {
                $bodyPages[] = $page;
                $page = [];
                $curPage++;
                continue;
            }
            // If our item is not a page break we add it to the page
            $item['page'] = $curPage;
            $page[] = $item;
        }
        // Add the last page
        $bodyPages[] = $page;

        return $bodyPages;
    }

    public function getPageCount()
    {
        return count($this->getPages());
    }

    public function getPage()
    {
        try {
            return $this->getPages()[$this->getRequestedPage() - 1];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the list of contents
     * @param $currentPageUrl
     * @return array
     */
    public function getContentsList($currentPageUrl)
    {
        if (!$this->show_contents) return [];

        $contentsList = [];
        foreach ($this->getPages() as $page) {
            foreach ($page as $item) {
                if (!empty($item['section']['in_contents'])) {

                    // Page 1 does not require the page param so just use the fragment
                    if ($item['page'] == 1) {
                        $url = "{$currentPageUrl}#{$item['section']['sId']}";
                    } else {
                        $url = "{$currentPageUrl}?page={$item['page']}#{$item['section']['sId']}";
                    }

                    $contentsList[] = [
                        'title' => $item['section']['heading'],
                        'page' => $item['page'],
                        'url' => $url,
                    ];
                }
            }
        }

        return $contentsList;
    }

    /**
     * Pagination for post pages
     * @param $currentPageUrl
     * @return LengthAwarePaginator
     */
    public function getPaginator($currentPageUrl)
    {
        $paginator = new LengthAwarePaginator(
            $this->getPage(),
            $this->getPageCount(),
            1,
            $this->getRequestedPage()
        );
        return $paginator->withPath($currentPageUrl);
    }

    public function getRequestedPage()
    {
        return (int) Input::get('page') ? (int) Input::get('page') : 1;
    }

    // Query scopes //

    public function scopeGetPostsList($query, $options)
    {
        $is_published = true;
        $sort = 'published_at desc';
        $categoryId = null;
        $subcategories = false;
        $searchQuery = null;
        $tagId = null;
        $postIds = null;
        $limit = false;
        $page = (int) Input::get('page') ? (int) Input::get('page') : 1;
        $perPage = 10;

        extract($options);

        $category = null;
        $categoryIds = [];
        $tag = null;

        if ($categoryId) $category = Category::where('id', $categoryId)->first();
        if ($tagId) $tag = Tag::where('id', $categoryId)->first();

        // Apply category filter
        if ($category) {
            if ($subcategories) {
                $categoryIds = [$category->getAllChildrenAndSelf()->lists('id')];
            } else {
                $categoryIds = [$category->id];
            }
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('id', $categoryIds);
            });
        }

        // Apply tag filter

        if ($tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->whereIn('id', [$tag->id]);
            });
        }

        if ($is_published) {
            $query->applyIsPublished();
        } else {
            $query->applyIsNotPublished();
        }

        // Specific post filter

        if ($postIds) {
            $query->whereIn('id', explode(',',$postIds))
                ->orderByRaw("FIELD(id, $postIds)");
        } elseif ($sort == '__random__') {
            $query->inRandomOrder();
        } else {
            @list($sortField, $sortDirection) = explode(' ', $sort);
            if (is_null($sortDirection)) {
                $sortDirection = "desc";
            }
            $query->orderBy($sortField, $sortDirection);
        }

        // This is an EXTREMELY basic search - There is no index on any of the searched columns
        // todo Implement a fast cross-db solution. Consider full text and generated (by php) column from title, excerpt and searchable body sections
        if ($searchQuery) {
            $query->where("title", "LIKE", "%{$searchQuery}%")
                ->orWhere("excerpt", "LIKE", "%{$searchQuery}%")
                ->orWhere("body", "LIKE", "%{$searchQuery}%");
        }

        $query->with('primary_category', 'tags');
        
        if ($limit) {
           return $query->limit($limit)->get();
        }

        return $query->paginate($perPage, $page);
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

    public function getLayout()
    {
        if ($this->cms_layout == "__inherit__" && Settings::get('defaultPostLayout') == '__inherit__') {
            // Inherit from category first.
            if ($this->primary_category) {
                return $this->primary_category->getLayout();
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

        $params = ['slug' => $this->slug];

        if ($this->primary_category) {
            // Provides a 'slug' for every depth of category
            $levels = array_reverse($this->primary_category->getPathToRoot());
            // category-x numbers up from the root category
            // parentcat-x numbers up from the primary category
            for ($depth = 0; $depth <= $this->primary_category->nest_depth; $depth++) {
                $reverse = $this->primary_category->nest_depth - $depth;
                $params["level-{$depth}"] = $levels[$depth]['slug'];
                if ($depth >= 0) {
                    $params["parent-{$depth}"] = $levels[$reverse]['slug'];
                }
            }
        }
        return strtolower(Controller::getController()->pageUrl($pageName, $params));
    }

    /**
     * Helper methods to determine the correct CMS page to
     * pass to the router. 5 Levels is more than enough and url truncation should
     * happen beyond this level of nesting
     *
     * @return array
     */
    private function getPostPage()
    {
        if (!$this->primary_category) {
            return $this->getZeroLevelPostPage();
        }
        if ($this->primary_category->nest_depth == 0) {
            return $this->getOneLevelPostPage();
        }
        if ($this->primary_category->nest_depth == 1) {
            return $this->getTwoLevelPostPage();
        }
        if ($this->primary_category->nest_depth == 2) {
            return $this->getThreeLevelPostPage();
        }
        if ($this->primary_category->nest_depth == 3) {
            return $this->getFourLevelPostPage();
        }
        if ($this->primary_category->nest_depth >= 4) {
            return $this->getFiveLevelPostPage();
        }
    }

    private function getZeroLevelPostPage()
    {
        return Settings::get('zeroLevelPostPage');
    }

    private function getOneLevelPostPage()
    {
        if (Settings::get('oneLevelPostPage')) {
            return Settings::get('oneLevelPostPage');
    }
        return $this->getZeroLevelPostPage();
    }

    private function getTwoLevelPostPage()
    {
        if (Settings::get('twoLevelPostPage')) {
            return Settings::get('twoLevelPostPage');
        }
        return $this->getOneLevelPostPage();
    }

    private function getThreeLevelPostPage()
    {
        if (Settings::get('threeLevelPostPage')) {
            return Settings::get('threeLevelPostPage');
        }
        return $this->getTwoLevelPostPage();
    }

    private function getFourLevelPostPage()
    {
        if (Settings::get('fourLevelPostPage')) {
            return Settings::get('fourLevelPostPage');
        }
        return $this->getThreeLevelPostPage();
    }

    private function getFiveLevelPostPage()
    {
        if (Settings::get('fiveLevelPostPage')) {
            return Settings::get('fiveLevelPostPage');
        }
        return $this->getFourLevelPostPage();
    }


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

        if ($type == 'all-posts-posts') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($type == 'category-posts-posts') {
            $references = [];

            $categories = Category::orderBy('name')->get();
            foreach ($categories as $category) {
                $references[$category->id] = $category->name;
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
        elseif ($item->type == 'all-posts-posts') {
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
        elseif ($item->type == 'category-posts-posts') {
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

            $query = self::isPublished()
                ->orderBy('title');

            $categories = $category->getAllChildrenAndSelf()->lists('id');
            $query->whereHas('categories', function($q) use ($categories) {
                $q->withoutGlobalScope(NestedTreeScope::class)->whereIn('id', $categories);
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


}
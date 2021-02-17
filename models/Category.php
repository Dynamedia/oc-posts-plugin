<?php namespace Dynamedia\Posts\Models;

use Model;

use Cms\Classes\Controller;
use Cms\Classes\Layout;
use Cms\Classes\Theme;
use Config;
use Input;
use Str;

/**
 * category Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;

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
        'slug' => 'required|unique:dynamedia_posts_categories|unique:dynamedia_posts_posts',
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

    public function beforeSave()
    {
        $this->slug = Str::slug($this->slug);
    }

    public function afterDelete()
    {
        $this->posts()->detach();
    }


    public function scopeHasPost($query, $post)
    {
        return $query->whereHas('posts', function ($p) use ($post) {

        });
    }

    public function getPathToRoot()
    {
        $path = [];

        foreach ($this->getParents() as $node) {
            $values = [];
            $values['id'] = $node->id;
            $values['name'] = $node->name;
            $values['slug'] = $node->slug;

            $path[] = $values;
        }

        $self = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug
        ];

        $path[] = $self;

        return array_reverse($path);
    }

    /**
     * Get a paginated collection of posts from this category and optionally
     * the subcategories of this category
     *
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function getPosts($options)
    {
        /*
        * Default options
        */

        $is_published = true;
        $sort = 'published_at desc';
        $categoryIds = [];
        $subcategories = false;
        $limit = false;
        $page = (int) Input::get('page') ? (int) Input::get('page') : 1;
        $perPage = 10;

        extract($options);

        if ($subcategories) {
            $categoryIds = [$this->getAllChildrenAndSelf()->lists('id')];
        } else {
            $categoryIds = [$this->id];
        }

        $query = Post::whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('id', $categoryIds);
        });

        if ($is_published) {
            $query->applyIsPublished();
        } else {
            $query->applyIsNotPublished();
        }

        if ($sort == '__random__') {
            $query->inRandomOrder();
        } else {
            @list($sortField, $sortDirection) = explode(' ', $sort);
            if (is_null($sortDirection)) {
                $sortDirection = "desc";
            }
            $query->orderBy($sortField, $sortDirection);
        }

        $query->with('primary_category', 'tags');
        
        if ($limit) {
           return $query->limit($limit)->get();
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Sets the "url" attribute with a URL to this object.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $pageName = $this->getCategoryPage();

        $params = ['slug' => $this->slug];

        // Sub category routing

        // Provides a 'slug' for every depth of category
        $levels = array_reverse($this->getPathToRoot());
        // category-x numbers up from the root category
        // parentcat-x numbers up from the primary category
        for ($depth = 0; $depth <= $this->nest_depth; $depth++) {
            $reverse = $this->nest_depth - $depth -1;
            $params["level-{$depth}"] = $levels[$depth]['slug'];
            if ($reverse >= 0) {
                $params["parent-{$depth}"] = $levels[$reverse]['slug'];
            }
        }
        return strtolower(Controller::getController()->pageUrl($pageName, $params));
    }

    public function getLayout()
    {
        if ($this->cms_layout == "__inherit__" && Settings::get('defaultCategoryLayout') == '__inherit__') {
            // No modifier
            return false;
        }
        elseif ($this->cms_layout == '__inherit__') {
            return Settings::get('defaultCategoryLayout');
        }
        else {
            return $this->cms_layout;
        }
    }


    /**
     * Helper methods to determine the correct CMS page to
     * pass to the router
     *
     * @return array
     */
    private function getCategoryPage()
    {
        if ($this->nest_depth == 0) {
            return $this->getZeroLevelCategoryPage();
        }
        if ($this->nest_depth == 1) {
            return $this->getOneLevelCategoryPage();
        }
        if ($this->nest_depth == 2) {
            return $this->getTwoLevelCategoryPage();
        }
        if ($this->nest_depth == 3) {
            return $this->getThreeLevelCategoryPage();
        }
        if ($this->nest_depth == 4) {
            return $this->getFourLevelCategoryPage();
        }
        if ($this->nest_depth >= 5) {
            return $this->getFiveLevelCategoryPage();
        }

    }

    private function getZeroLevelCategoryPage()
    {
            return Settings::get('zeroLevelCategoryPage');
    }
    
    private function getOneLevelCategoryPage()
    {
        if (Settings::get('oneLevelCategoryPage')) {
            return Settings::get('oneLevelCategoryPage');
        }
        return $this->getZeroLevelCategoryPage();
    }

    private function getTwoLevelCategoryPage()
    {
        if (Settings::get('twoLevelCategoryPage')) {
            return Settings::get('twoLevelCategoryPage');
        }
        return $this->getOneLevelCategoryPage();
    }

    private function getThreeLevelCategoryPage()
    {
        if (Settings::get('threeLevelCategoryPage')) {
            return Settings::get('threeLevelCategoryPage');
        }
        return $this->getTwoLevelCategoryPage();
    }

    private function getFourLevelCategoryPage()
    {
        if (Settings::get('fourLevelCategoryPage')) {
            return Settings::get('fourLevelCategoryPage');
        }
        return $this->getThreeLevelCategoryPage();
    }

    private function getFiveLevelCategoryPage()
    {
        if (Settings::get('fiveLevelCategoryPage')) {
            return Settings::get('fiveLevelCategoryPage');
        }
        return $this->getFourLevelCategoryPage();
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

        if ($type == 'all-posts-categories') {
            $result = [
                'dynamicItems' => true
            ];
        }

        return $result;
    }

    protected static function listSubCategoryOptions()
    {
        // From Rainlab.Posts plugin
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
        elseif ($item->type == 'all-posts-categories') {
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
}

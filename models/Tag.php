<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Models\Settings;
use Model;
use Str;
use Dynamedia\Posts\Models\Post;
use Input;
use Config;
use Cms\Classes\Controller;

/**
 * tag Model
 */
class Tag extends Model
{
    use \October\Rain\Database\Traits\Validation;

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
    public $rules = [];

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
            'table' => 'dynamedia_posts_posts_tags',
            'order' => 'title',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // For tag widget
    public function beforeSave()
    {
        if (!$this->slug && $this->name) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function afterDelete()
    {
        $this->posts()->detach();
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
        $pageName = Settings::get('tagPage');

        $params = ['slug' => $this->slug];

        return strtolower(Controller::getController()->pageUrl($pageName, $params));
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
        $sort = 'published_at';
        $limit = false;
        $page = (int) Input::get('page') ? (int) Input::get('page') : 1;
        $perPage = 10;

        extract($options);

        $query = $this->posts();

        if ($is_published) {
            $query->applyIsPublished();
        } else {
            $query->applyIsNotPublished();
        }

        $query->orderBy($sort, 'DESC');

        if ($limit) $query->limit($limit);

        $query->with('primary_category', 'tags');

        return $query->paginate($perPage, $page);
    }
}

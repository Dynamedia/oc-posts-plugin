<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Models\Settings;
use Model;
use Str;
use Dynamedia\Posts\Models\Post;
use Input;
use Config;
use Cms\Classes\Controller;
use BackendAuth;
use ValidationException;

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
        $sort = 'published_at desc';
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
}

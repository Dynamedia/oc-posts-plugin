<?php namespace Dynamedia\Posts\Models;

use Model;
use ValidationException;

/**
 * PostSlug Model
 */
class PostSlug extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_post_slugs';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = ['slug', 'post_id'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'slug'  => 'required',
        'post'  => 'required'
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array hasOne and other relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'post' => ['Dynamedia\Posts\Models\Post'],
    ];
    public $belongsToMany = [
        'posttranslations' => [
            'Dynamedia\Posts\Models\PostTranslation',
            'table' => 'dynamedia_posts_post_trans_slug',
            'key'       => 'slug_id',
            'otherKey'  => 'trans_id',
            'order' => 'id'
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static function isAvailable($postId, $slug)
    {
        $available = false;

        $takenPost = self::where('slug', $slug)
            ->where('post_id', '<>', $postId)
            ->count();

        $takenCategory = CategorySlug::where('slug', $slug)
            ->count();

        if (!$takenPost && !$takenCategory) {
            $available = true;
        }

        return $available;
    }
}

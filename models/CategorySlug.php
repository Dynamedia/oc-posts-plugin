<?php namespace Dynamedia\Posts\Models;

use Model;

/**
 * CategorySlug Model
 */
class CategorySlug extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_category_slugs';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = ['slug', 'category_id'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'slug'      => 'required',
        'category'  => 'required'
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
        'category' => ['Dynamedia\Posts\Models\Category'],
    ];
    public $belongsToMany = [
        'categorytranslations' => [
            'Dynamedia\Posts\Models\CategoryTranslation',
            'table' => 'dynamedia_posts_category_trans_slug',
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

    public static function isAvailable($categoryId, $slug)
    {
        $available = false;

        $takenCategory = self::where('slug', $slug)
            ->where('category_id', '<>', $categoryId)
            ->count();

        $takenPost = PostSlug::where('slug', $slug)
            ->count();

        if (!$takenCategory && !$takenPost) {
            $available = true;
        }

        return $available;
    }
}

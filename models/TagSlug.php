<?php namespace Dynamedia\Posts\Models;

use Model;

/**
 * TagSlug Model
 */
class TagSlug extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_tag_slugs';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = ['slug', 'tag_id'];

    /**
     * @var array rules for validation
     */
    public $rules = [];

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
        'tag' => ['Dynamedia\Posts\Models\Tag'],
    ];
    public $belongsToMany = [
        'tagtranslations' => [
            'Dynamedia\Posts\Models\TagTranslation',
            'table' => 'dynamedia_posts_tag_trans_slug',
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

    public static function isAvailable($tagId, $slug)
    {
        $available = false;

        $takenTag = self::where('slug', $slug)
            ->where('tag_id', '<>', $tagId)
            ->count();

        if (!$takenTag) {
            $available = true;
        }

        return $available;
    }
}

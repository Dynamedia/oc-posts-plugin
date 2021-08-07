<?php namespace Dynamedia\Posts\Models;

use Model;
use RainLab\Translate\Models\Locale;
use ValidationException;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use October\Rain\Database\Traits\Validation;

/**
 * PostTranslation Model
 */
class PostTranslation extends Model
{
    use SeoTrait, ImagesTrait, ControllerTrait, Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_post_translations';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [];

    public $rules = [];

    public $customMessages = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [
        'body',
        'images',
        'seo'
    ];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

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
        'native' => ['Dynamedia\Posts\Models\Post'],
        'locale' => ['Rainlab\Translate\Models\Locale']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // todo move this into a custom validation rule
    public function beforeValidate()
    {
        if (empty($this->slug)) {
            throw new ValidationException(['slug' => 'The slug is required']);
        }
        $takenPost = Post::where('slug', $this->slug)
            ->where('id', '<>', $this->native->id)
            ->count();

        // A post can have the same slug as its own translations
        $takenPostTranslation = PostTranslation::where('slug', $this->slug)
            ->whereHas('native', function ($q) {
                $q->where('id', '<>', $this->native->id);
            })
            ->count();

        $takenCategory = Category::where('slug', $this->slug)
            ->count();

        $takenCategoryTranslation = CategoryTranslation::where('slug', $this->slug)
            ->count();

        if ($takenPost || $takenPostTranslation || $takenCategory || $takenCategoryTranslation) {
            throw new ValidationException(['slug' => 'This slug has already been taken']);
        }
    }

    // todo get this moved and minify it?
    public function getLocaleIdOptions()
    {
        $alreadyTranslated = [];
        if (!empty($this->native->translations)) {
            foreach ($this->native->translations as $translation) {
                if ($translation->id != $this->id) {
                    $alreadyTranslated[] = $translation->locale->id;
                }
            }
        }

        $locales = Locale::where('is_default', '<>', 1)
            ->whereNotIn('id', $alreadyTranslated)
            ->order()
            ->pluck('name', 'id')
            ->all();

        return $locales;
    }
}

<?php namespace Dynamedia\Posts\Models;

use Model;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use \October\Rain\Database\Traits\Validation;
use RainLab\Translate\Models\Locale;
use ValidationException;

/**
 * CategoryTranslation Model
 */
class CategoryTranslation extends Model
{
    use SeoTrait, ImagesTrait, ControllerTrait, Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_category_translations';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [];

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
    protected $jsonable = [
        'body',
        'images',
        'seo',
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
        'native' => ['Dynamedia\Posts\Models\Category'],
        'locale' => ['Rainlab\Translate\Models\Locale'],
    ];
    public $belongsToMany = [
        'categoryslugs' => [
            'Dynamedia\Posts\Models\CategorySlug',
            'table' => 'dynamedia_posts_category_trans_slug',
            'key'       => 'trans_id',
            'otherKey'  => 'slug_id',
            'order' => 'id'
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // todo move this into a custom validation rule
    public function beforeValidate()
    {
        if (!CategorySlug::isAvailable($this->native->id, $this->slug)) {
            throw new ValidationException(['slug' => "Slug is not available"]);
        }
    }

    public function afterSave()
    {
        $slug = $this->native->categoryslugs()->firstOrCreate([
            'slug' => $this->slug,
        ]);
        $this->categoryslugs()->attach($slug);
    }

    public function beforeDelete()
    {
        // Remove the pivot record but don't attempt to delete the slug record. It can still resolve to the tag
        $this->categoryslugs()->detach();
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

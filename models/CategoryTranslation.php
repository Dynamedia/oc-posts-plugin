<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Classes\Body\Body;
use Model;
use BackendAuth;
use Event;
use Dynamedia\Posts\Traits\SeoTrait;
use Dynamedia\Posts\Traits\ImagesTrait;
use Dynamedia\Posts\Traits\ControllerTrait;
use \October\Rain\Database\Traits\Validation;
use phpDocumentor\Reflection\Types\Self_;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;
use ValidationException;
use Str;

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
    public $rules = [
        'native' =>'required',
        'locale' => 'required',
        'slug'  => 'required'
    ];

    public $customMessages = [
        'native.required' => 'Translations cannot be created without a category.',
        'locale.required' => 'Translations must specify their locale',
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [
        'body_document',
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

    public function beforeValidate()
    {
        Event::fire('dynamedia.posts.categorytranslation.validating', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.categorytranslation.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterSave()
    {
        Event::fire('dynamedia.posts.categorytranslation.saved', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.categorytranslation.deleting', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterDelete()
    {
        Event::fire('dynamedia.posts.categorytranslation.deleted', [$this, $user = BackendAuth::getUser()]);
    }

    /**
     * Return the options for the available translation locales
     *
     * @return array
     */
    public function getLocaleIdOptions()
    {
        $usedIds = [];
        $formVars = post('Category');
        if (!empty($formVars)) {
            $parentCategory = Category::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();

            $usedIds[] = Translator::instance()->getDefaultLocale();
            foreach ($parentCategory->translations as $translation) {
                $usedIds[] = $translation->locale_id;
            }
        }

        $locales = Locale::whereNotIn('id', $usedIds)
            ->whereNotIn('code', [Translator::instance()->getDefaultLocale()])
            ->order()
            ->pluck('name', 'id')
            ->all();

        return $locales;
    }

    public function filterFields($fields, $context = null)
    {
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
    }

    public function getPopulateFromOptions() {
        $options = [
            '__blank__' => 'Blank',
        ];

        $formVars = post('Category');
        if (!empty($formVars)) {
            $parentCategory = Category::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();
        } else {
            return $options;
        }

        $options['__native__'] = Locale::where('code', Translator::instance()->getDefaultLocale())->first()->name;

        foreach ($parentCategory->translations as $translation) {
            $options["{$translation->id}"] = $translation->locale->name;
        }

        return $options;
    }

    /**
     * Pre-populate the translatable fields from native, or existing translation
     */
    public function prePopulateAttributes()
    {
        // We can use this later to hook into Google translate
        $formVars = post('CategoryTranslation');
        if (empty($formVars['_populateFrom'])) {
            return;
        }

        if ($formVars['_populateFrom'] == "__blank__") {
            // Not truly blank as we need a slug and title
            $this->slug = $this->native->slug;
            $this->name = $this->native->name;
            $this->body_document = ['body_type' => 'repeater_body'];
            // Finish here, we're not populating anything else
            return;

        } elseif ($formVars['_populateFrom'] == "__native__") {
            $source = $this->native;
        }

        else {
            $source = self::where('id', $formVars['_populateFrom'])->first();
        }

        if (empty($source)) return;

        $this->attributes['slug'] = $source->attributes['slug'];
        $this->attributes['name'] = $source->attributes['name'];
        $this->attributes['excerpt'] = $source->attributes['excerpt'];
        $this->attributes['body_document'] = $source->attributes['body_document'];
        $this->attributes['images'] = $source->attributes['images'];
        $this->attributes['seo'] = $source->attributes['seo'];
    }

    /**
     * @return mixed body object by body_document body_type
     */
    public function getBodyAttribute()
    {
        $body = Body::getBody($this);
        return $body;
    }

    public function getBodyCacheKey()
    {
        return $this->native->getBodyCacheKey();
    }

    public function setSchema()
    {
        $this->native->setSchema();
    }

    /**
     * Get the url of the translation.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->native->getUrlInLocale($this->locale->code);
    }
}

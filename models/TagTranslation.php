<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Classes\Body\Body;
use Model;
use BackendAuth;
use Event;
use October\Rain\Database\Traits\Validation;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;
use ValidationException;
use Str;

/**
 * TagTranslation Model
 */
class TagTranslation extends Model
{
    use Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 'dynamedia_posts_tag_translations';

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
        'native.required' => 'Translations cannot be created without a tag.',
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
        'native' => ['Dynamedia\Posts\Models\Tag'],
        'locale' => ['Rainlab\Translate\Models\Locale'],
    ];
    public $belongsToMany = [
        'tagslugs' => [
            'Dynamedia\Posts\Models\TagSlug',
            'table' => 'dynamedia_posts_tag_trans_slug',
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
        Event::fire('dynamedia.posts.tagtranslation.validating', [$this, $user = BackendAuth::getUser()]);
        $this->slug = Str::slug($this->slug);

        if (!TagSlug::isAvailable($this->native->id, $this->slug)) {
            throw new ValidationException(['slug' => "Slug is not available"]);
        }
        $this->prePopulateAttributes();
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.tagtranslation.saving', [$this, $user = BackendAuth::getUser()]);
        $this->body_text = $this->body->getTextContent();
        $this->slug = Str::slug($this->slug);
    }

    public function afterSave()
    {
        $slug = $this->native->tagslugs()->firstOrCreate([
            'slug' => $this->slug,
        ]);
        $this->tagslugs()->sync($slug->id, false);

        $this->native->invalidateTranslatedAttributesCache();
        $this->native->invalidateBodyCache();
        Event::fire('dynamedia.posts.tagtranslation.saved', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.tagtranslation.deleting', [$this, $user = BackendAuth::getUser()]);
        // Remove the pivot record but don't attempt to delete the slug record. It can still resolve to the tag
        $this->tagslugs()->detach();
    }

    public function afterDelete()
    {
        Event::fire('dynamedia.posts.tagtranslation.deleted', [$this, $user = BackendAuth::getUser()]);
    }

    /**
     * Return the options for the available translation locales
     *
     * @return array
     */
    public function getLocaleIdOptions()
    {
        $usedIds = [];
        $formVars = post('Tag');
        if (!empty($formVars)) {
            $parentTag = Tag::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();

            $usedIds[] = Translator::instance()->getDefaultLocale();
            foreach ($parentTag->translations as $translation) {
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

        $formVars = post('Tag');
        if (!empty($formVars)) {
            $parentTag = Tag::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();
        } else {
            return $options;
        }

        $options['__native__'] = Locale::where('code', Translator::instance()->getDefaultLocale())->first()->name;

        foreach ($parentTag->translations as $translation) {
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
        $formVars = post('TagTranslation');
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

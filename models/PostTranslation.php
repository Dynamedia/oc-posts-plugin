<?php namespace Dynamedia\Posts\Models;

use BackendAuth;
use Event;
use Dynamedia\Posts\Classes\Body\Body;
use Model;
use RainLab\Translate\Models\Locale;
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

    public $rules = [
        'native' =>'required',
        'locale' => 'required',
        'slug'  => 'required'
    ];

    public $customMessages = [
        'native.required' => 'Translations cannot be created without a post.',
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
    public $belongsToMany = [
        'postslugs' => [
            'Dynamedia\Posts\Models\PostSlug',
            'table' => 'dynamedia_posts_post_trans_slug',
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
        Event::fire('dynamedia.posts.posttranslation.validating', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.posttranslation.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterSave()
    {
        Event::fire('dynamedia.posts.posttranslation.saved', [$this, $user = BackendAuth::getUser()]);
    }

    public function beforeDelete()
    {
        Event::fire('dynamedia.posts.posttranslation.deleting', [$this, $user = BackendAuth::getUser()]);
    }

    public function afterDelete()
    {
        Event::fire('dynamedia.posts.posttranslation.deleted', [$this, $user = BackendAuth::getUser()]);
    }

    /**
     * Return the options for the available translation locales
     *
     * @return array
     */
    public function getLocaleIdOptions()
    {
        $usedIds = [];
        $formVars = post('Post');
        if (!empty($formVars)) {
            $parentPost = Post::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();

            $usedIds[] = $formVars['locale'];
            foreach ($parentPost->translations as $translation) {
                $usedIds[] = $translation->locale_id;
            }
        }

        $locales = Locale::whereNotIn('id', $usedIds)
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
                $fields->template_body->hidden = true;
                $fields->template_body_options->hidden = true;
            }
            elseif ($fields->body_type->value == 'richeditor_body') {
                $fields->repeater_body->hidden = true;
                $fields->richeditor_body->hidden = false;
                $fields->markdown_body->hidden = true;
                $fields->template_body_options->hidden = true;
                $fields->template_body->hidden = true;
            }
            elseif ($fields->body_type->value == 'markdown_body') {
                $fields->markdown_body->hidden = false;
                $fields->repeater_body->hidden = true;
                $fields->richeditor_body->hidden = true;
                $fields->template_body_options->hidden = true;
                $fields->template_body->hidden = true;
            }
            elseif ($fields->body_type->value == 'template_body') {
                $fields->template_body->hidden = false;
                $fields->template_body_options->hidden = false;
                $fields->repeater_body->hidden = true;
                $fields->richeditor_body->hidden = true;
                $fields->markdown_body->hidden = true;
            }
            else {
                $fields->repeater_body->hidden = false;
                $fields->richeditor_body->hidden = true;
                $fields->markdown_body->hidden = true;
                $fields->template_body->hidden = true;
                $fields->template_body_options->hidden = true;
            }
        }
    }

    public function getPopulateFromOptions() {
        $options = [
            '__blank__' => 'Blank',
        ];

        $formVars = post('Post');
        if (!empty($formVars)) {
            $parentPost = Post::where('slug', $formVars['slug'])
                ->with('translations')
                ->first();
        } else {
            return $options;
        }

        $options['__native__'] = $parentPost->locale->name;

        foreach ($parentPost->translations as $translation) {
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
        $formVars = post('PostTranslation');
        if (empty($formVars['_populateFrom'])) {
            return;
        }

        if ($formVars['_populateFrom'] == "__blank__") {
            // Not truly blank as we need a slug and title
            $this->slug = $this->native->slug;
            $this->title = $this->native->title;
            $this->body_document = ['body_type' => 'repeater_body'];
            // Finish here, we're not populating anything else
            return;

        } elseif ($formVars['_populateFrom'] == "__native__") {
            $source = $this->native;
        }

        else {
            $source = PostTranslation::where('id', $formVars['_populateFrom'])->first();
        }

        if (empty($source)) return;

        $this->attributes['slug'] = $source->attributes['slug'];
        $this->attributes['title'] = $source->attributes['title'];
        $this->attributes['excerpt'] = $source->attributes['excerpt'];
        $this->attributes['body_document'] = $source->attributes['body_document'];
        $this->attributes['images'] = $source->attributes['images'];
        $this->attributes['seo'] = $source->attributes['seo'];
        $this->attributes['show_contents'] = $source->show_contents;
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

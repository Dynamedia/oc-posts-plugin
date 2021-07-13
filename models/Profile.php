<?php namespace Dynamedia\Posts\Models;

use Model;
use Backend\Models\User as BackendUser;
use Dynamedia\Posts\Traits\ControllerTrait;
use \October\Rain\Database\Traits\Validation;
use URL;

use ValidationException;

/**
 * Profile Model
 */
class Profile extends Model
{
    use Validation, ControllerTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dynamedia_posts_profiles';

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
        'username'  => 'required|unique:dynamedia_posts_profiles'
    ];

    public $customMessages = [
        'username.unique' => 'This username is not available'
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
        'url',
        'schema',
        'fullName',
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'id',
        'user_id',
        'updated_at'
    ];

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
    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getFullNameAttribute()
    {
        return "{$this->user->first_name} {$this->user->last_name}";
    }

    // per https://octobercms.com/support/article/ob-10
    public static function getFromUser($user)
    {
        if ($user->profile) {
            return;
        }

        $profile = new static;
        $profile->user = $user;
        // We need a username that is not a login. User will want to set their own
        // todo this needs to be unique. Currently highly probable but not guaranteed
        $profile->username = strtolower($user->last_name . $user->first_name) . rand(0,999);

        $profile->save();

        $user->profile = $profile;

        return $profile;
    }

    public function beforeValidate()
    {
        // model validation not working against backend_users table. todo fix
        if (BackendUser::where('login', $this->username)->first()) {
            throw new ValidationException(['message' => $this->customMessages['username.unique']]);
        }
    }

    /**
     * Sets the "url" attribute with a URL to this object.
     *
     * @param array $params Override request URL parameters
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $params = [
            'postsUsername' => $this->username
        ];
        if (!Settings::get('userPage')) return "/";
        return strtolower($this->getController()->pageUrl(Settings::get('userPage'), $params));
    }

    public function getSchemaAttribute()
    {
        $schema = [
            "@context"  => "https://schema.org",
            "@type"     => "Person",
            "name"      => "{$this->user->first_name} {$this->user->last_name}",
            "url"       => $this->website_url ? $this->website_url : $this->url,
        ];
        if (isset($this->user->avatar)) {
            $schema['image'] = $this->user->avatar->path;
        }
        if (!empty($this->twitter_handle)  || !empty($this->facebook_handle) || !empty($this->instagram_handle)) {
            $sameAs = [];
            if (!empty($this->twitter_handle)) $sameAs[] = "https://twitter.com/{$this->getAsUsername($this->twitter_handle)}";
            if (!empty($this->instagram_handle)) $sameAs[] = "https://instagram.com/{$this->getAsUsername($this->instagram_handle)}";
            if (!empty($this->facebook_handle)) $sameAs[] = "https://facebook.com/{$this->getAsUsername($this->facebook_handle)}";
            $schema["sameAs"] = $sameAs;
        }

        return $schema;
    }

    /**
     * Get a handle or username as a username
     *
     * @param false $handle
     * @return false|string
     */
    public function getAsUsername($handle = false)
    {
        if (!$handle) return false;

        $username = trim($handle);

        if (starts_with($username, "@")) {
            return substr($username, 1);
        } else {
            return $username;
        }
    }

    /**
     * Get a handle or username as a handle
     *
     * @param false $username
     * @return false|string
     */
    public function getAsHandle($username = false)
    {
        if (!$username) return false;

        $handle = trim($username);

        if (starts_with($handle, "@")) {
            return $handle;
        } else {
            return "@{$handle}";
        }
    }

}

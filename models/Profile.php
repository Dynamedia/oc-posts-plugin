<?php namespace Dynamedia\Posts\Models;

use Model;
use Backend\Models\User as BackendUser;
use Dynamedia\Posts\Traits\ControllerTrait;
use \October\Rain\Database\Traits\Validation;

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
        'username'  => 'required|unique:dynamedia_posts_profile'
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
        'url'
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

    // per https://octobercms.com/support/article/ob-10
    public static function getFromUser($user)
    {
        if ($user->profile) {
            return;
        }

        $profile = new static;
        $profile->user = $user;
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

        return strtolower($this->getController()->pageUrl(Settings::get('userPage'), $params));
    }

}

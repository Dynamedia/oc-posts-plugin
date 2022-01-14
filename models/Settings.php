<?php namespace Dynamedia\Posts\Models;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use Model;
use Cms\Classes\Page;
use Str;
use ValidationException;
use October\Rain\Database\Traits\Validation;
use BackendAuth;
use Event;

/**
 * Settings Model
 */
class Settings extends Model
{
    use Validation;

    public $implement = [
        'System.Behaviors.SettingsModel',
        '@RainLab.Translate.Behaviors.TranslatableModel'
        ];

    public $translatable = [
        'rssTitle',
        'rssDescription'
    ];


    // A unique code
    public $settingsCode = 'dynamedia_posts_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public $rules = [
        'publisherUrl' =>  'url',
    ];

    public function beforeSave()
    {
        Event::fire('dynamedia.posts.settings.saving', [$this, $user = BackendAuth::getUser()]);
    }

    public function resetDefault()
    {
        if (!AccessControl::userCanManageSettings(BackendAuth::getUser())) {
            throw new ValidationException([
                'error' => "Insufficient permissions to edit settings"
            ]);
        }
    }

    public function filterFields($fields, $context = null)
    {
        if (!AccessControl::userCanManageSettings(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->readOnly = true;
            }
        }
        if (!AccessControl::userCanViewSettings(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->hidden = true;
            }
        }
    }


    public function getTagPageOptions()
    {
        $pages = Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayTag')) return false;
                if (!Str::contains($page->url, 'postsTagSlug')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }
    public function getPostPageOptions()
    {
        $pages =  Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayPost')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }


    public function getCategoryPageOptions()
    {
        $pages =  Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayCategory')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }

    public function getUserPageOptions()
    {
        $pages = Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayUser')) return false;
                if (!Str::contains($page->url, 'postsUsername')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }

    public function getRssPostCountOptions()
    {
        $options = [];
        for ($i = 10; $i <= 30; $i++) {
            $options["$i"] = "$i";
        }
        return $options;
    }

}


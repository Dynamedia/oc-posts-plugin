<?php namespace Dynamedia\Posts\Models;

use Model;
use Cms\Classes\Page;
use Str;
use ValidationException;
use BackendAuth;

/**
 * Settings Model
 */
class Settings extends Model
{
    //use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'dynamedia_posts_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public function beforeSave()
    {
        if (!$this->userCanManage(BackendAuth::getUser())) {
            throw new ValidationException([
                'error' => "Insufficient permissions to edit settings"
            ]);
        }
    }

    public function resetDefault()
    {
        if (!$this->userCanManage(BackendAuth::getUser())) {
            throw new ValidationException([
                'error' => "Insufficient permissions to edit settings"
            ]);
        }
    }

    public function filterFields($fields, $context = null)
    {
        if (!$this->userCanManage(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->readOnly = true;
            }
        }
        if (!$this->userCanView(BackendAuth::getUser())) {
            foreach ($fields as $field) {
                $field->hidden = true;
            }
        }
    }

    /**
     * Check if user has required permissions to manage settings
     * @param $user
     * @return bool
     */
    private function userCanManage($user)
    {
        if (!$user->hasAccess('dynamedia.posts.manage_settings')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to view settings
     * @param $user
     * @return bool
     */
    private function userCanView($user)
    {
        if (!$user->hasAccess('dynamedia.posts.view_settings')) {
            return false;
        } else {
            return true;
        }
    }

    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayTag')) return false;
                if (!Str::contains($page->url, 'postsTagSlug')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');
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

    public function getPostPageWithoutCategoryOptions()
    {
        return $this->getPostPageOptions();
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

}


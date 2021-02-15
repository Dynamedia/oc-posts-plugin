<?php namespace Dynamedia\Posts\Models;

use Model;
use Cms\Classes\Page;
use Str;

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


    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayTag')) return false;
                if (!Str::contains($page->url, 'slug')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');
    }
    public function getZeroLevelPostPageOptions()
    {
        // displayPost pages with no category slugs
        $pages =  Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayPost')) return false;
                if (!Str::contains($page->url, 'slug')) return false;
                if (Str::contains($page->url, ':level-')
                    || Str::contains($page->url, ':parent-')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }

    
    public function getMultiLevelPostPageOptions()
    {
        $pages =  Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayPost')) return false;
                if (!Str::contains($page->url, 'slug')) return false;
                if (!Str::contains($page->url, ':level-')
                    && !Str::contains($page->url, ':parent-')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }
    
    public function getOneLevelPostPageOptions()
    {
        return $this->getMultiLevelPostPageOptions();
    }


    public function getTwoLevelPostPageOptions()
    {
        return $this->getMultiLevelPostPageOptions();
    }

    public function getThreeLevelPostPageOptions()
    {
        return $this->getMultiLevelPostPageOptions();
    }

    public function getFourLevelPostPageOptions()
    {
        return $this->getMultiLevelPostPageOptions();
    }

    public function getFiveLevelPostPageOptions()
    {
        return $this->getMultiLevelPostPageOptions();
    }

    public function getZeroLevelCategoryPageOptions()
    {
        $pages =  Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayCategory')) return false;
                if (!Str::contains($page->url, ':slug')) return false;
                if (Str::contains($page->url, ':level-')
                    || Str::contains($page->url, ':parent-')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }

    public function getMultiLevelCategoryPageOptions()
    {
        $pages = Page::sortBy('baseFileName')
            ->filter(function ($page) {
                if (!$page->hasComponent('displayCategory')) return false;
                if (!Str::contains($page->url, ':slug')) return false;
                if (!Str::contains($page->url, ':level-') 
                    && !Str::contains($page->url, 'parent-')) return false;
                return true;
            })
            ->lists('baseFileName', 'baseFileName');

        return array_merge(['' => 'None'], $pages);
    }

    public function getOneLevelCategoryPageOptions()
    {
        return $this->getMultiLevelCategoryPageOptions();
    }
    
    public function getTwoLevelCategoryPageOptions()
    {
        return $this->getMultiLevelCategoryPageOptions();
    }

    public function getThreeLevelCategoryPageOptions()
    {
        return $this->getMultiLevelCategoryPageOptions();
    }

    public function getFourLevelCategoryPageOptions()
    {
        return $this->getMultiLevelCategoryPageOptions();
    }

    public function getFiveLevelCategoryPageOptions()
    {
        return $this->getMultiLevelCategoryPageOptions();
    }

}

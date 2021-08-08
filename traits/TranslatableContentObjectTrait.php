<?php namespace Dynamedia\Posts\Traits;

use RainLab\Translate\Classes\Translator;

Trait TranslatableContentObjectTrait
{
    public function getActiveTranslationAttribute()
    {
        if (!empty($this->translations)) {
            return $this->translations->reject(function ($value, $key) {
                return empty($value->locale) || $value->locale->code != Translator::instance()->getLocale();
            })->first();
        }
        return null;
    }

    public function getTitleAttribute($value)
    {
        if (array_key_exists('title', $this->attributes)) {
            return $this->getTranslated('title', $value);
        } else {
            return $this->getTranslated('name', $value);
        }
    }

    public function getNameAttribute($value)
    {
        if (array_key_exists('name', $this->attributes)) {
            return $this->getTranslated('name', $value);
        } else {
            return $this->getTranslated('title', $value);
        }
    }

    public function getSlugAttribute($value)
    {
        return $this->getTranslated('slug', $value);
    }

    public function getExcerptAttribute($value)
    {
        return $this->getTranslated('excerpt', $value);
    }

    public function getImagesAttribute($value)
    {
        return $this->getTranslated('images', $value);
    }

    public function getSeoAttribute($value)
    {
        return $this->getTranslated('seo', $value);
    }

    public function getCmsLayoutAttribute($value)
    {
        return $this->getTranslated('cms_layout', $value);
    }

    private function getTranslated($attribute, $default)
    {
        if ($this->active_translation) {
            return $this->active_translation->attributes[$attribute];
        } else {
            return $default;
        }
    }
}

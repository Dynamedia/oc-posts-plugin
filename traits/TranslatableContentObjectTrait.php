<?php namespace Dynamedia\Posts\Traits;

use RainLab\Translate\Classes\Translator;

Trait TranslatableContentObjectTrait
{
    public function getActiveTranslationAttribute()
    {
        return $this->getTranslation(Translator::instance()->getLocale());
    }

    public function getTranslation($locale = null)
    {

        if (!empty($this->translations)) {
            return $this->translations->reject(function ($value, $key)use ($locale) {
                return empty($value->locale) || $value->locale->code != $locale;
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

    public function getBodyDocumentAttribute($value) {
        return $this->getTranslated('body_document', $value);
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

    public function getTranslated($attribute, $default, $locale = null)
    {
        if ($locale) {
            $trans = $this->getTranslation($locale);
            if ($trans && !app()->runningInBackend()){
                return $trans->attributes[$attribute];
            }
        }
        // Do not attempt to translate attributes in the backend - We never want that.
        elseif ($this->active_translation && !app()->runningInBackend()) {
            return $this->active_translation->attributes[$attribute];
        } else {
            return $default;
        }
    }

}

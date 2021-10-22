<?php namespace Dynamedia\Posts\Traits;

use Dynamedia\Posts\Classes\Body\Body;
use RainLab\Translate\Classes\Translator;
use Cache;
use Carbon\Carbon;
use RainLab\Translate\Models\Locale;

Trait TranslatableContentObjectTrait
{
    /**
     * Return an array of translated attributes for the model
     *
     * @param string $locale
     * @return array
     */
    public function getTranslatedAttributes($locale)
    {
        if (empty($locale)) $locale = Translator::instance()->getLocale();

        $cacheKey = $this->getTranslatedAttributesCacheKey($locale);
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        $attributes = [];

        if (!empty($this->translations)) {
            $translation = $this->translations->reject(function ($value, $key) use ($locale) {
                return empty($value->locale) || $value->locale->code != $locale;
            })->first();

            if (!empty($translation->attributes)) {
                $attributes = $translation->attributes;
            }
        }

        Cache::put($cacheKey, $attributes, Carbon::now()->addHours(1));
        return $attributes;
    }

    /**
     * Remove all translated attributes from the cache for this model
     *
     */
    public function invalidateTranslatedAttributesCache()
    {
        foreach (Locale::all() as $locale) {
            $cacheKey = $this->getTranslatedAttributesCacheKey($locale->code);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Return the cache key for storing translated attributes
     *
     * @param string $locale
     * @return string
     */
    private function getTranslatedAttributesCacheKey($locale)
    {
        return md5(self::class . "_{$this->id}_translated_attributes_{$locale}");
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


    /**
     * Return the translated attribute.
     * Specify whether to return the native attribute where no translation exists
     *
     * @param $attribute
     * @param $default
     * @param null $locale
     * @param false $fallback
     * @return mixed|null
     */
    public function getTranslated($attribute, $default, $locale = null, $fallback = false)
    {
        // Do not attempt to translate attributes in the backend - We never want that.
        if (app()->runningInBackend()) return $default;

        $translatedAttributes = $this->getTranslatedAttributes($locale);

        $value = null;

        if (!empty($translatedAttributes)) {
            if (!empty($translatedAttributes[$attribute])) {
                $value = $translatedAttributes[$attribute];
            } elseif ($fallback) {
                $value = $this->attributes[$attribute];
            }

        } elseif ($fallback) {
            $value = $this->attributes[$attribute];
        }

         else {
            $value = $default;
        }

        return $value;
    }

    /**
     * @return mixed body object by body_document body_type
     */
    public function getBodyAttribute()
    {
        $body = Body::getBody($this);
        return $body;
    }

    public function getBodyCacheKey($locale = null)
    {
        $nonLocalised = get_class($this) . "_{$this->id}_body";
        if ($locale) {
            $localised = $nonLocalised . $locale->code;
        } else {
            $localised = get_class($this) . "_{$this->id}_body" . Translator::instance()->getLocale();
        }
        return md5($localised);
    }

    public function invalidateBodyCache() {
        foreach (Locale::all() as $locale) {
            $cacheKey = $this->getBodyCacheKey($locale);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Get all locale variations
     *
     * @return mixed
     */
    public function getAlternateLocales()
    {
        $locales[] = [
            'code' => Translator::instance()->getDefaultLocale(),
            'url'  => $this->getUrlInLocale(Translator::instance()->getDefaultLocale()),
            'default' => true
        ];

        foreach ($this->translations as $translation) {
            // Handle case where translation still exists but language has been deleted
            if (empty($translation->locale)) continue;

            $locales[] = [
                'code' => $translation->locale->code,
                'url' => $translation->url,
            ];
        }

        return $locales;
    }
}

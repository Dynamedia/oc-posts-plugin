<?php namespace Dynamedia\Posts\Traits;

use Dynamedia\Posts\Classes\Seo\Seo;

Trait SeoTrait {

    public function invalidateSeoCache()
    {
        foreach ($this->getAlternateLocales() as $locale) {
            Seo::invalidateCache($locale['url']);
        }
    }
}

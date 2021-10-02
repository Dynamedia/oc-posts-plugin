<?php namespace Dynamedia\Posts\Traits;

use Carbon\Carbon;
use Dynamedia\Posts\Classes\Seo\PostsObjectSeoParser;
use RainLab\Translate\Classes\Translator;
use Cache;
use RainLab\Translate\Models\Locale;

Trait SeoTrait {
    
    public function getHtmlHeadAttribute()
    {
        $cacheKey = $this->getHtmlHeadAttributeCacheKey();
        if (Cache::has($cacheKey)) return \Cache::get($cacheKey);

        $seoData = new PostsObjectSeoParser($this);
        $view = \View::make('dynamedia.posts::seo.head_seo', [
            'search' => $seoData->getSearchArray(),
            'openGraph' => $seoData->getOpenGraphArray(),
            'twitter' => $seoData->getTwitterArray(),
            'themeData' => $seoData->getThemeData(),
            'locales' => $this->getAlternateLocales()
        ])->render();

        Cache::put($cacheKey, $view, Carbon::now()->addHours(1));
        return $view;
    }

    private function getHtmlHeadAttributeCacheKey($locale = null)
    {
        if (!$locale) {
            $locale = Translator::instance()->getLocale();
        }
        return md5(self::class . "_{$this->id}_html_head_attribute_{$locale}");
    }

    /**
     * Remove all translated attributes from the cache for this model
     *
     */
    public function invalidateHtmlHeadAttributeCache()
    {
        foreach (Locale::all() as $locale) {
            $cacheKey = $this->getHtmlHeadAttributeCacheKey($locale->code);
            Cache::forget($cacheKey);
        }
    }
}

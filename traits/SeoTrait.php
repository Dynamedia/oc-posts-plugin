<?php namespace Dynamedia\Posts\Traits;

use Carbon\Carbon;
use Dynamedia\Posts\Classes\Seo\PostsObjectSeoParser;
use RainLab\Translate\Classes\Translator;
use Cache;
use RainLab\Translate\Models\Locale;

Trait SeoTrait {

    /**
     * Get the SEO title for this object
     *
     * @return string|null
     */
    public function getSeoSearchTitleAttribute()
    {
        if (!empty($this->seo['title'])) {
            return $this->seo['title'];
        } elseif ($this->getObjectTitle()) {
            return $this->getObjectTitle();
        } else {
            return $this->getPageTitle();
        }
    }


    /**
     *
     * Get the SEO description for this object
     * @return string|null
     */
    public function getSeoSearchDescriptionAttribute()
    {
        if (!empty($this->seo['description'])) {
            return $this->seo['description'];
        } elseif ($this->excerpt) {
            return strip_tags($this->excerpt);
        } else {
            return strip_tags($this->getPageDescription());
        }
    }

    /**
     * Get the OpenGraph title. Defaults to Twitter > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoOpengraphTitleAttribute($fallback = false) {
        if ($fallback) {
            if (!empty($this->seo['opengraph_title'])) {
                return $this->seo['opengraph_title'];
            }
            return null;
        }

        if (!empty($this->seo['opengraph_title'])) {
            return $this->seo['opengraph_title'];
        } elseif ($this->getSeoTwitterTitleAttribute($fallback = true)) {
            return $this->getSeoTwitterTitleAttribute($fallback = true);
        } else {
            return $this->getSeoSearchTitleAttribute();
        }
    }

    /**
     * Get the Twitter title. Defaults to Opengraph > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoTwitterTitleAttribute($fallback = false) {
        if ($fallback) {
            if (!empty($this->seo['twitter_description'])) {
                return $this->seo['twitter_description'];
            }
            return null;
        }

        if (!empty($this->seo['twitter_title'])) {
            return $this->seo['twitter_title'];
        } elseif ($this->getSeoOpengraphTitleAttribute($fallback = true)) {
            return $this->getSeoOpengraphTitleAttribute($fallback = true);
        } else {
            return $this->getSeoSearchTitleAttribute();
        }
    }

    /**
     * Get the OpenGraph description. Defaults to Twitter > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoOpengraphDescriptionAttribute($fallback = false) {
        if ($fallback) {
            if (!empty($this->seo['opengraph_description'])) {
                return $this->seo['opengraph_description'];
            }
            return null;
        }

        if (!empty($this->seo['opengraph_description'])) {
            return $this->seo['opengraph_description'];
        } elseif ($this->getSeoTwitterDescriptionAttribute($fallback = true)) {
            return $this->getSeoTwitterDescriptionAttribute($fallback = true);
        } else {
            return $this->getSeoSearchDescriptionAttribute();
        }
    }

    /**
     * Get the Twitter title. Defaults to Opengraph > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoTwitterDescriptionAttribute($fallback = false) {
        if ($fallback) {
            if (!empty($this->seo['twitter_description'])) {
                return $this->seo['twitter_description'];
            }
            return null;
        }

        if (!empty($this->seo['twitter_description'])) {
            return $this->seo['twitter_description'];
        } elseif ($this->getSeoOpengraphDescriptionAttribute($fallback = true)) {
            return $this->getSeoOpengraphDescriptionAttribute($fallback = true);
        } else {
            return $this->getSeoSearchDescriptionAttribute();
        }
    }

    public function getSeoTwitterImageAttribute()
    {
        if ($this->getTwitterImage()) return $this->getTwitterImage();
        if ($this->getFacebookImage()) return $this->getFacebookImage();
        if ($this->getBannerImage()) return $this->getBannerImage();
        if ($this->getListImage()) return $this->getListImage();
        if ($this->getThemeTwitterImage()) return $this->getThemeTwitterImage();
        if ($this->getThemeFacebookImage()) return $this->getThemeFacebookImage();
        return null;
    }

    public function getSeoOpengraphImageAttribute()
    {
        if ($this->getFacebookImage()) return $this->getFacebookImage();
        if ($this->getTwitterImage()) return $this->getTwitterImage();
        if ($this->getBannerImage()) return $this->getBannerImage();
        if ($this->getListImage()) return $this->getListImage();
        if ($this->getThemeFacebookImage()) return $this->getThemeFacebookImage();
        if ($this->getThemeTwitterImage()) return $this->getThemeTwitterImage();
        return null;
    }

    /**
     * Get the 'title' of the object.
     *
     * Only Posts have true titles. Categories and Tags have names
     *
     * @return string|null
     */
    private function getObjectTitle()
    {
        // Categories and tags have names. Posts have titles.
        if (!empty($this->title)) {
            $objectTitle = $this->title;
        } elseif (!empty($this->name)) {
            $objectTitle = $this->name;
        } else {
            $objectTitle = null;
        }

        return $objectTitle;
    }

    /**
     * Get the title of the current CMS page
     *
     * @return string|null
     */
    public function getPageTitle()
    {
        $page = $this->getCmsPage();

        if (!$page) return null;

        if ($page->meta_title) {
            return $this->meta_title;
        } else {
            return $page->title;
        }
    }

    /**
     * Get the description of the current CMS page
     *
     * @return string|null
     */
    public function getPageDescription()
    {
        $page = $this->getCmsPage();

        if (!$page) return null;

        if ($page->meta_description) {
            return $this->meta_description;
        } else {
            return $page->description;
        }
    }

    /**
     * Get the CMS page attached to the controller
     *
     * @return Cms\Classes\Page|null
     */
    protected function getCmsPage() {
        $cmsPage = null;

        if ($this->getController() instanceof \Cms\Classes\Controller) {
            if (!empty($this->getController()->getPage())) {
                $cmsPage = $this->getController()->getPage();
            }
        }

        return $cmsPage;
    }

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

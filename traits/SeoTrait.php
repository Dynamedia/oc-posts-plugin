<?php namespace Dynamedia\Posts\Traits;

Trait SeoTrait {

    /**
     * Get the SEO title for this object
     *
     * @return string|null
     */
    public function getSeoSearchTitle()
    {
        if ($this->seo['title']) {
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
    public function getSeoSearchDescription()
    {
        if ($this->seo['description']) {
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
    public function getSeoOpengraphTitle($fallback = false) {
        if ($fallback) {
            if ($this->seo['opengraph_title']) {
                return $this->seo['opengraph_title'];
            }
            return null;
        }

        if ($this->seo['opengraph_title']) {
            return $this->seo['opengraph_title'];
        } elseif ($this->getSeoTwitterTitle($fallback = true)) {
            return $this->getSeoTwitterTitle($fallback = true);
        } else {
            return $this->getSeoSearchTitle();
        }
    }

    /**
     * Get the Twitter title. Defaults to Opengraph > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoTwitterTitle($fallback = false) {
        if ($fallback) {
            if ($this->seo['twitter_description']) {
                return $this->seo['twitter_description'];
            }
            return null;
        }

        if ($this->seo['twitter_title']) {
            return $this->seo['twitter_title'];
        } elseif ($this->getSeoOpengraphTitle($fallback = true)) {
            return $this->getSeoOpengraphTitle($fallback = true);
        } else {
            return $this->getSeoSearchTitle();
        }
    }

    /**
     * Get the OpenGraph description. Defaults to Twitter > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoOpengraphDescription($fallback = false) {
        if ($fallback) {
            if ($this->seo['opengraph_description']) {
                return $this->seo['opengraph_description'];
            }
            return null;
        }

        if ($this->seo['opengraph_description']) {
            return $this->seo['opengraph_description'];
        } elseif ($this->getSeoTwitterDescription($fallback = true)) {
            return $this->getSeoTwitterDescription($fallback = true);
        } else {
            return $this->getSeoSearchDescription();
        }
    }

    /**
     * Get the Twitter title. Defaults to Opengraph > SeoSearch title
     *
     * @return mixed|string|null
     */
    public function getSeoTwitterDescription($fallback = false) {
        if ($fallback) {
            if ($this->seo['twitter_description']) {
                return $this->seo['twitter_description'];
            }
            return null;
        }

        if ($this->seo['twitter_description']) {
            return $this->seo['twitter_description'];
        } elseif ($this->getSeoOpengraphDescription($fallback = true)) {
            return $this->getSeoOpengraphDescription($fallback = true);
        } else {
            return $this->getSeoSearchDescription();
        }
    }

    public function getSeoTwitterImage()
    {
        if ($this->getTwitterImage()) return $this->getTwitterImage();
        if ($this->getFacebookImage()) return $this->getFacebookImage();
        if ($this->getBannerImage()) return $this->getBannerImage();
        if ($this->getListImage()) return $this->getListImage();
        if ($this->getThemeTwitterImage()) return $this->getThemeTwitterImage();
        if ($this->getThemeFacebookImage()) return $this->getThemeFacebookImage();
        return null;
    }

    public function getSeoFacebookImage()
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
}

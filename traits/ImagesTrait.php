<?php namespace Dynamedia\Posts\Traits;

Trait ImagesTrait {

    public function getBannerImage()
    {
        if (!empty($this->images['banner']['default'])) {
            return $this->images['banner']['default'];
        }
        return null;
    }

    public function getBannerImageResponsive()
    {
        if (!empty($this->images['banner']['responsive'])) {
            return $this->images['banner']['responsive'];
        }
        return [];
    }

    public function getListImage()
    {
        if (!empty($this->images['list']['default'])) {
            return $this->images['list']['default'];
        }
        return null;
    }

    public function getListImageResponsive()
    {
        if (!empty($this->images['list']['responsive'])) {
            return $this->images['list']['responsive'];
        }
        return [];
    }

    public function getTwitterImage()
    {
        if (!empty($this->images['social']['twitter'])) {
            return $this->images['social']['twitter'];
        }
        return null;
    }

    public function getFacebookImage()
    {
        if (!empty($this->images['social']['facebook'])) {
            return $this->images['social']['facebook'];
        }
        return null;
    }

    public function getThemeFacebookImage()
    {
        if (!empty(($this->getController()->getTheme()->getCustomData()->images['social']['facebook']))) {
            return $this->getController()->getTheme()->getCustomData()->images['social']['facebook'];
        }
        return null;
    }

    public function getThemeTwitterImage()
    {
        if (!empty(($this->getController()->getTheme()->getCustomData()->images['banner']['default']))) {
            return $this->getController()->getTheme()->getCustomData()->images['banner']['default'];
        }
        return null;
    }

    public function getThemeBannerImage()
    {
        if (!empty(($this->getController()->getTheme()->getCustomData()->images['banner']['default']))) {
            return $this->getController()->getTheme()->getCustomData()->images['banner']['default'];
        }
        return null;
    }

    public function getThemeBannerImageResponsive()
    {
        if (!empty(($this->getController()->getTheme()->getCustomData()->images['banner']['responsive']))) {
            return $this->getController()->getTheme()->getCustomData()->images['banner']['responsive'];
        }
        return null;
    }

    public function getBestImage()
    {
        if ($this->getBannerImage()) return $this->getBannerImage();
        if ($this->getListImage()) return $this->getListImage();
        if ($this->getFacebookImage()) return $this->getFacebookImage();
        if ($this->getTwitterImage()) return $this->getTwitterImage();
        if ($this->getThemeBannerImage()) return $this->getThemeBannerImage();
        if ($this->getThemeBannerImage()) return $this->getThemeTwitterImage();
        if ($this->getThemeFacebookImage()) return $this->getThemeFacebookImage();
        return null;
    }

}
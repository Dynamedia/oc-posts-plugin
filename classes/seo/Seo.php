<?php
namespace Dynamedia\Posts\Classes\Seo;

use Cms\Classes\Controller;
use Cms\Classes\Page;
use App;
use Dynamedia\Posts\Classes\Seo\Schema\ExtendedGraph;
use Media\Classes\MediaLibrary;
use RainLab\Translate\Classes\Translator;

class Seo
{
    protected $controller;
    protected $page;
    protected $themeData;
    protected $translator;

    protected $schemaGraph;
    protected $url;
    protected $alternativeUrls = [];
    protected $searchTitle;
    protected $searchDescription;
    protected $openGraphTitle;
    protected $openGraphDescription;
    protected $openGraphImage;
    protected $twitterSite;
    protected $twitterCreator;
    protected $twitterTitle;
    protected $twitterDescription;
    protected $twitterImage;


    public function __construct()
    {
        $this->schemaGraph = new ExtendedGraph();
        $this->translator = Translator::instance();
    }

    public function getSchemaGraph()
    {
        return $this->schemaGraph;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function setFallbackProperties($controller)
    {
        $this->controller = $controller;
        $this->themeData = $this->controller->getTheme()->getCustomData();
        $this->page = $this->controller->getPage();

        $this->setFallbackSearchTitle();
        $this->setFallbackSearchDescription();
        $this->setFallbackOpenGraphTitle();
        $this->setFallbackOpenGraphDescription();
        $this->setFallbackUrl();
        $this->setFallbackOpenGraphImage();
        $this->setFallbackTwitterSite();
        $this->setFallbackTwitterCreator();
        $this->setFallbackTwitterTitle();
        $this->setFallbackTwitterDescription();
        $this->setFallbackTwitterImage();
        $this->setFallbackAlternativeUrls();
        $this->loadGraph();
    }

    public function loadGraph()
    {
        $this->schemaGraph->getWebpage()
            ->setProperty("@id", $this->url . "#wepbage")
            ->url($this->url)
            ->title($this->searchTitle)
            ->description($this->searchDescription);
    }


    public function setSearchTitle($title)
    {
        $this->searchTitle = $title;
    }

    protected function setFallbackSearchTitle()
    {
        if (!$this->searchTitle && $this->page) {
            if (!empty($this->page->attributes['meta_title'])) {
                $this->searchTitle = $this->page->attributes['meta_title'];
            } elseif(!empty($this->page->attributes['title'])) {
                $this->searchTitle = $this->page->attributes['title'];
            }
        }
    }

    public function getSearchTitle()
    {
        return $this->searchTitle;
    }

    public function setSearchDescription($description)
    {
        $this->searchDescription = $description;
    }

    protected function setFallbackSearchDescription()
    {
        if (!$this->searchDescription && $this->page) {
            if (!empty($this->page->attributes['meta_description'])) {
                $this->searchDescription = $this->page->attributes['meta_description'];
            } elseif(!empty($this->page->attributes['title'])) {
                $this->searchDescription = $this->page->attributes['title'];
            }
        }
    }

    public function getSearchDescription()
    {
        return $this->searchDescription;
    }

    public function setOpenGraphTitle($title)
    {
        $this->openGraphTitle = $title;
    }

    protected function setFallbackOpenGraphTitle()
    {
        if (!$this->openGraphTitle) {
            $this->openGraphTitle = $this->searchTitle;
        }
    }

    public function getOpenGraphTitle()
    {
        return $this->openGraphTitle;
    }

    public function setOpenGraphDescription($description)
    {
        $this->openGraphDescription = $description;
    }

    protected function setFallbackOpenGraphDescription()
    {
        if (!$this->openGraphDescription) {
            $this->openGraphDescription = $this->searchDescription;
        }
    }

    public function getOpenGraphDescription()
    {
        return $this->openGraphDescription;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    protected function setFallbackUrl()
    {
        if (!$this->url && $this->controller) {
            $this->url = $this->controller->currentPageUrl();
        }
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setAlternativeUrls($urls)
    {
        $this->alternativeUrls = $urls;
    }

    //todo - copy rainlab logic probably
    public function setFallbackAlternativeUrls()
    {
        if (!$this->alternativeUrls){
            $this->alternativeUrls = [];
        }
    }

    public function getAlternativeUrls()
    {
        return $this->alternativeUrls;
    }

    public function setOpenGraphImage($image)
    {
        $this->openGraphImage = $image;
    }

    protected function setFallbackOpenGraphImage()
    {
        if (!$this->openGraphImage) {
            $path = false;
            if (!empty($this->themeData->images['social']['facebook'])) {
                $path = $this->themeData->images['social']['facebook'];
            } elseif (!empty($this->themeData->images['social']['twitter'])) {
                $path = $this->themeData->images['social']['twitter'];
            } elseif (!empty($this->themeData->images['banner']['default'])) {
                $path = $this->themeData->images['banner']['default'];
            }
            if ($path) {
                $this->openGraphImage = MediaLibrary::url($path);
            }
        }
    }

    public function getOpenGraphImage()
    {
        return $this->openGraphImage;
    }

    public function setTwitterSite($handle)
    {
        $this->twitterSite = $handle;
    }

    protected function setFallbackTwitterSite()
    {
        if (!$this->twitterSite) {
            if (!empty($this->themeData->twitter_handle)) {
                $this->twitterSite = $this->themeData->twitter_handle;
            }
        }
    }

    public function getTwitterSite()
    {
        return $this->twitterSite;
    }

    public function setTwitterCreator($handle)
    {
        $this->twitterCreator = $handle;
    }

    protected function setFallbackTwitterCreator()
    {
        if (!$this->twitterCreator) {
            $this->twitterCreator = $this->twitterSite;
        }
    }

    public function getTwitterCreator()
    {
        return $this->twitterCreator;
    }

    public function setTwitterTitle($title)
    {
        $this->twitterTitle = $title;
    }

    protected function setFallbackTwitterTitle()
    {
        if (!$this->twitterTitle) {
            $this->twitterTitle = $this->searchTitle;
        }
    }

    public function getTwitterTitle()
    {
        return $this->twitterTitle;
    }

    public function setTwitterDescription($description)
    {
        $this->twitterDescription = $description;
    }

    protected function setFallbackTwitterDescription()
    {
        if (!$this->twitterDescription) {
            $this->twitterDescription = $this->searchDescription;
        }
    }

    public function getTwitterDescription()
    {
        return $this->twitterDescription;
    }

    public function setTwitterImage($image)
    {
        $this->twitterImage = $image;
    }

    protected function setFallbackTwitterImage()
    {
        if (!$this->twitterImage) {
            $path = false;
            if (!empty($this->themeData->images['social']['twitter'])) {
                $path = $this->themeData->images['social']['twitter'];
            } elseif (!empty($this->themeData->images['social']['facebook'])) {
                $path = $this->themeData->images['social']['facebook'];
            } elseif (!empty($this->themeData->images['banner']['default'])) {
                $path = $this->themeData->images['banner']['default'];
            }
            if ($path) {
                $this->twitterImage = MediaLibrary::url($path);
            }
        }
    }

    public function getTwitterImage()
    {
        return $this->twitterImage;
    }

    public function getThemeData()
    {
        return $this->themeData->attributes;
    }



}

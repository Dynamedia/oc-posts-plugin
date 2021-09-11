<?php


namespace Dynamedia\Posts\Classes\Seo;


use Cms\Classes\Controller;
use RainLab\Translate\Classes\Translator;
use Cms\Classes\MediaLibrary;
use Spatie\SchemaOrg\Schema;


class StaticPagesSeoParser extends Seo
{


    public function __construct($staticPage)
    {
        $this->page = $staticPage;
        $this->controller = Controller::getController();
        $this->themeData = $this->controller->getTheme()->getCustomData();
        $this->setProperties();
    }

    private function setProperties()
    {
        $this->setSearchTitle();
        $this->setSearchDescription();
        $this->setOpenGraphTitle();
        $this->setOpenGraphDescription();
        $this->setOpenGraphUrl();
        $this->setOpenGraphImage();
        $this->setTwitterSite();
        $this->setTwitterCreator();
        $this->setTwitterTitle();
        $this->setTwitterDescription();
        $this->setTwitterImage();
        $this->setSchema();
    }

    private function setSearchTitle()
    {
        if (!empty($this->page->meta_title)) {
            $this->appendSearch('title', $this->page->meta_title);
        } elseif ($this->page->title) {
            $this->appendSearch('title', $this->page->title);
        } else {
            $this->appendSearch('title', '');
        }
    }

    private function setSearchDescription()
    {
        if (!empty($this->page->seo['search_description'])) {
            $this->appendSearch('description', $this->page->seo['search_description']);
        } elseif ($this->page->meta_description) {
            $this->appendSearch('description', $this->page->meta_description);
        } elseif (!empty($this->getSearch('title'))) {
            $this->appendSearch('description', $this->getSearch('title'));
        } else {
            $this->appendSearch('description', '');
        }
    }

    private function setOpenGraphTitle()
    {
        if (!empty($this->page->seo['opengraph_title'])) {
            $this->appendOpenGraph('title', $this->page->seo['opengraph_title']);
        } elseif (!empty($this->page->seo['twitter_title'])) {
            $this->appendOpenGraph('title', $this->page->seo['twitter_title']);
        } else {
            $this->appendOpenGraph('title', $this->getSearch('title'));
        }
    }

    private function setOpenGraphDescription()
    {
        if (!empty($this->page->seo['opengraph_description'])) {
            $this->appendOpenGraph('description', $this->page->seo['opengraph_description']);
        } elseif (!empty($this->page->seo['twitter_title'])) {
            $this->appendOpenGraph('description', $this->page->seo['twitter_description']);
        } else {
            $this->appendOpenGraph('description', $this->getSearch('description'));
        }
    }

    private function setOpenGraphUrl()
    {
        $locale = Translator::instance()->getLocale();
        $url = url(Translator::instance()
            ->getPathInLocale($this->page->url, $locale));
        $this->appendOpenGraph('url', $url);
    }

    private function setOpenGraphImage()
    {
        $path = false;
        if (!empty($this->page->facebook_image)) {
            $path = $this->page->facebook_image;
        } elseif (!empty($this->page->twitter_image)) {
            $path= $this->page->twitter_image;
        } elseif (!empty($this->page->banner_image)) {
            $path = $this->page->banner_image;
        } elseif (!empty($this->themeData->images['social']['facebook'])) {
            $path = $this->themeData->images['social']['facebook'];
        } elseif (!empty($this->themeData->images['social']['twitter'])) {
            $path = $this->themeData->images['social']['twitter'];
        } elseif (!empty($this->themeData->images['banner']['default'])) {
            $path = $this->themeData->images['banner']['default'];
        }
        if ($path) {
            $this->appendOpenGraph('image', MediaLibrary::url($path));
        }
    }

    private function setTwitterSite()
    {
        if (!empty($this->themeData->twitter_handle))
        {
            $this->appendTwitter('site', $this->themeData->twitter_handle);
        }
    }

    private function setTwitterCreator()
    {
        $this->appendTwitter('creator', $this->getTwitter('site'));
    }

    private function setTwitterTitle()
    {
        if (!empty($this->page->seo['twitter_title'])) {
            $this->appendTwitter('title', $this->page->seo['twitter_title']);
        } elseif (!empty($this->page->seo['opengraph_title'])) {
            $this->appendTwitter('title', $this->page->seo['opengraph_title']);
        } else {
            $this->appendTwitter('title', $this->getSearch('title'));
        }
    }

    private function setTwitterDescription()
    {
        if (!empty($this->page->seo['twitter_description'])) {
            $this->appendTwitter('description', $this->page->seo['twitter_description']);
        } elseif (!empty($this->page->seo['opengraph_description'])) {
            $this->appendTwitter('description', $this->page->seo['opengraph_description']);
        } else {
            $this->appendTwitter('description', $this->getSearch('description'));
        }
    }

    private function setTwitterImage()
    {
        $path = false;
        if (!empty($this->page->twitter_image)) {
            $path = $this->page->twitter_image;
        } elseif (!empty($this->page->facebook_image)) {
            $path= $this->page->facebook_image;
        } elseif (!empty($this->page->banner_image)) {
            $path = $this->page->banner_image;
        } elseif (!empty($this->themeData->images['social']['twitter'])) {
            $path = $this->themeData->images['social']['twitter'];
        } elseif (!empty($this->themeData->images['social']['facebook'])) {
            $path = $this->themeData->images['social']['facebook'];
        } elseif (!empty($this->themeData->images['banner']['default'])) {
            $path = $this->themeData->images['banner']['default'];
        }
        if ($path) {
            $this->appendTwitter('image', MediaLibrary::url($path));
        }
    }

    private function setSchema()
    {
        //dd($this->page);
        $about = schema::thing()
            ->name($this->page->seo['about']);

        $webPage = schema::webPage()
            ->about($about)
            ->dateModified($this->page->mtime);

        $this->schema = $webPage->toArray();
    }

}

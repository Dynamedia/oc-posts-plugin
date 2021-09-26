<?php


namespace Dynamedia\Posts\Classes\Seo;


use Cms\Classes\Controller;
use Cms\Classes\MediaLibrary;
use Dynamedia\Posts\Classes\Seo\Schema\Article;
use Spatie\SchemaOrg\Schema;
use Dynamedia\Posts\Classes\Seo\Schema\SchemaFactory;

class PostsObjectSeoParser extends Seo
{
    public function __construct($model)
    {
        $this->model = $model;
        $this->controller = Controller::getController();
        if ($this->controller) {
            $this->page = $this->controller->getPage();
            $this->themeData = $this->controller->getTheme()->getCustomData();
        }
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
    }

    private function setSearchTitle()
    {
        if (!empty($this->model->seo['title'])) {
            $this->appendSearch('title', $this->model->seo['title']);
        } elseif ($this->model->title) {
            $this->appendSearch('title', $this->model->title);
        } elseif ($this->model->name) {
            $this->appendSearch('title', $this->model->name);
        }
        elseif ($this->page) {
            if (!empty($this->page->attributes['meta_title'])) {
                $this->appendSearch('title', $this->page->attributes['meta_title']);
            } elseif(!empty($this->page->attributes['meta_title'])) {
                $this->appendSearch('title', $this->page->attributes['title']);
            }
        }
    }

    private function setSearchDescription()
    {
        if (!empty($this->model->seo['search_description'])) {
            $this->appendSearch('description', $this->model->seo['search_description']);
        } elseif ($this->model->excerpt) {
            $this->appendSearch('description', strip_tags($this->model->excerpt));
        } elseif ($this->page) {
            if (!empty($this->page->attributes['meta_description'])) {
                $this->appendSearch('description', $this->page->attributes['meta_description']);
            } elseif(!empty($this->page->attributes['title'])) {
                $this->appendSearch('description', $this->page->attributes['title']);
            }
        }
    }

    private function setOpenGraphTitle()
    {
        if (!empty($this->model->seo['opengraph_title'])) {
            $this->appendOpenGraph('title', $this->model->seo['opengraph_title']);
        } elseif (!empty($this->model->seo['twitter_title'])) {
            $this->appendOpenGraph('title', $this->model->seo['twitter_title']);
        } else {
            $this->appendOpenGraph('title', $this->getSearch('title'));
        }
    }

    private function setOpenGraphDescription()
    {
        if (!empty($this->model->seo['opengraph_description'])) {
            $this->appendOpenGraph('description', $this->model->seo['opengraph_description']);
        } elseif (!empty($this->model->seo['twitter_title'])) {
            $this->appendOpenGraph('description', $this->model->seo['twitter_description']);
        } else {
            $this->appendOpenGraph('description', $this->getSearch('description'));
        }
    }

    private function setOpenGraphUrl()
    {
        if (!empty($this->model->url)) {
            $this->appendOpenGraph('url', $this->model->url);
        } elseif ($this->controller) {
            $this->appendOpenGraph('url', $this->controller->currentPageUrl());
        }
    }

    private function setOpenGraphImage()
    {
        $path = false;
        if (!empty($this->model->images['social']['facebook'])) {
            $path = $this->model->images['social']['facebook'];
        } elseif (!empty($this->model->images['social']['twitter'])) {
            $path= $this->model->images['social']['twitter'];
        } elseif (!empty($this->model->images['banner']['default'])) {
            $path = $this->model->images['banner']['default'];
        } elseif (!empty($this->themeData->images['social']['facebook'])) {
            $path =  $this->themeData->images['social']['facebook'];
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
        if (!empty($this->model->author->profile->twitter_handle)) {
            $this->appendTwitter('creator', $this->model->author->profile->twitter_handle);
        } else {
            $this->appendTwitter('creator', $this->getTwitter('site'));
        }
    }

    private function setTwitterTitle()
    {
        if (!empty($this->model->seo['twitter_title'])) {
            $this->appendTwitter('title', $this->model->seo['twitter_title']);
        } elseif (!empty($this->model->seo['opengraph_title'])) {
            $this->appendTwitter('title', $this->model->seo['opengraph_title']);
        } else {
            $this->appendTwitter('title', $this->getSearch('title'));
        }
    }

    private function setTwitterDescription()
    {
        if (!empty($this->model->seo['twitter_description'])) {
            $this->appendTwitter('description', $this->model->seo['twitter_description']);
        } elseif (!empty($this->model->seo['opengraph_description'])) {
            $this->appendTwitter('description', $this->model->seo['opengraph_description']);
        } else {
            $this->appendTwitter('description', $this->getSearch('description'));
        }
    }

    private function setTwitterImage()
    {
        $path = false;
        if (!empty($this->model->images['social']['twitter'])) {
            $path =  $this->model->images['social']['twitter'];
        } elseif (!empty($this->model->images['social']['facebook'])) {
            $path = $this->model->images['social']['facebook'];
        } elseif (!empty($this->model->images['banner']['default'])) {
            $path = $this->model->images['banner']['default'];
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

}

<?php

namespace Dynamedia\Posts\Classes\Seo;


use Media\Classes\MediaLibrary;
use App;

class PostsObjectSeoParser
{
    protected $seo;

    public function __construct($model)
    {
        $this->model = $model;
        $this->seo = App::make('dynamedia.posts.seo');
    }

    public function getSeoObject()
    {
        return $this->seo;
    }

    protected function setProperties()
    {
        $this->setSearchTitle();
        $this->setSearchDescription();
        $this->setOpenGraphTitle();
        $this->setOpenGraphDescription();
        $this->setUrl();
        $this->setOpenGraphImage();
        $this->setTwitterCreator();
        $this->setTwitterTitle();
        $this->setTwitterDescription();
        $this->setTwitterImage();
        $this->setAlternativeUrls();
    }

    protected function setSearchTitle()
    {
        if (!empty($this->model->seo['title'])) {
            $this->seo->setSearchTitle($this->model->seo['title']);
        } elseif ($this->model->title) {
            $this->seo->setSearchTitle($this->model->title);
        } elseif ($this->model->name) {
            $this->seo->setSearchTitle($this->model->name);
        }
    }

    protected function setSearchDescription()
    {
        if (!empty($this->model->seo['search_description'])) {
            $this->seo->setSearchDescription($this->model->seo['search_description']);
        } elseif ($this->model->excerpt) {
            $this->seo->setSearchDescription(strip_tags($this->model->excerpt));
        }
    }

    protected function setOpenGraphTitle()
    {
        if (!empty($this->model->seo['opengraph_title'])) {
            $this->seo->setOpenGraphTitle($this->model->seo['opengraph_title']);
        } elseif (!empty($this->model->seo['twitter_title'])) {
            $this->seo->setOpenGraphTitle($this->model->seo['twitter_title']);
        }
    }

    protected function setOpenGraphDescription()
    {
        if (!empty($this->model->seo['opengraph_description'])) {
            $this->seo->setOpenGraphDescription($this->model->seo['opengraph_description']);
        } elseif (!empty($this->model->seo['twitter_title'])) {
            $this->seo->setOpenGraphDescription($this->model->seo['twitter_description']);
        }
    }

    protected function setUrl()
    {
        if (!empty($this->model->url)) {
            $this->seo->setUrl($this->model->url);
        }
    }

    protected function setOpenGraphImage()
    {
        $path = false;
        if (!empty($this->model->images['social']['facebook'])) {
            $path = $this->model->images['social']['facebook'];
        } elseif (!empty($this->model->images['social']['twitter'])) {
            $path= $this->model->images['social']['twitter'];
        } elseif (!empty($this->model->images['banner']['default'])) {
            $path = $this->model->images['banner']['default'];
        }
        if ($path) {
            $this->seo->setOpenGraphImage(MediaLibrary::url($path));
        }
    }

    protected function setTwitterCreator()
    {
        if (!empty($this->model->author->profile->twitter_handle)) {
            $this->seo->setTwitterCreator($this->model->author->profile->twitter_handle);
        }
    }

    protected function setTwitterTitle()
    {
        if (!empty($this->model->seo['twitter_title'])) {
            $this->seo->setTwitterTitle($this->model->seo['twitter_title']);
        } elseif (!empty($this->model->seo['opengraph_title'])) {
            $this->seo->setTwittertitle($this->model->seo['opengraph_title']);
        }
    }

    protected function setTwitterDescription()
    {
        if (!empty($this->model->seo['twitter_description'])) {
            $this->seo->setTwitterDescription($this->model->seo['twitter_description']);
        } elseif (!empty($this->model->seo['opengraph_description'])) {
            $this->seo->setTwitterDescription($this->model->seo['opengraph_description']);
        }
    }

    protected function setTwitterImage()
    {
        $path = false;
        if (!empty($this->model->images['social']['twitter'])) {
            $path =  $this->model->images['social']['twitter'];
        } elseif (!empty($this->model->images['social']['facebook'])) {
            $path = $this->model->images['social']['facebook'];
        } elseif (!empty($this->model->images['banner']['default'])) {
            $path = $this->model->images['banner']['default'];
        }

        if ($path) {
            $this->seo->setTwitterImage(MediaLibrary::url($path));
        }
    }

    protected function setAlternativeUrls()
    {
        $this->seo->setAlternativeUrls($this->model->getAlternateLocales());
    }


}

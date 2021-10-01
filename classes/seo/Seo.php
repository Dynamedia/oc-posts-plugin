<?php
namespace Dynamedia\Posts\Classes\Seo;

use Cms\Classes\Theme;
use Cms\Classes\Controller;
use Media\Classes\MediaLibrary;

class Seo
{
    protected $controller;
    protected $model;
    protected $page;
    protected $themeData;

    protected $search = [];
    protected $openGraph = [];
    protected $twitter = [];
    protected $schema = [];

    public function __construct($model)
    {
    }



    protected function appendSearch($key, $value)
    {
        $this->search[$key] = $value;
    }

    protected function getSearch($key)
    {
        if (!empty($this->search[$key])) {
            return $this->search[$key];
        }
        return "";
    }

    public function getSearchArray()
    {
        return $this->search;
    }

    protected function appendOpenGraph($key, $value)
    {
        $this->openGraph[$key] = $value;
    }

    protected function getOpenGraph($key)
    {
        if (!empty($this->openGraph[$key])) {
            return $this->openGraph[$key];
        }
        return "";
    }

    public function getOpenGraphArray()
    {
        return $this->openGraph;
    }

    protected function appendTwitter($key, $value)
    {
        $this->twitter[$key] = $value;
    }

    protected function getTwitter($key)
    {
        if (!empty($this->twitter[$key])) {
            return $this->twitter[$key];
        }
        return "";
    }

    public function getTwitterArray()
    {
        return $this->twitter;
    }

    public function getSchemaArray()
    {
        return $this->schema;
    }

    public function getThemeData()
    {
        return $this->themeData->attributes;
    }



}

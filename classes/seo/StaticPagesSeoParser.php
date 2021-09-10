<?php


namespace Dynamedia\Posts\Classes\Seo;


use Cms\Classes\Controller;

class StaticPagesSeoParser extends Seo
{
    private $controller;
    private $model;
    private $page;
    private $themeData;

    private $search = [];
    private $openGraph = [];
    private $twitter = [];
    private $schema = [];

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

}

<?php
namespace Dynamedia\Posts\Classes\Seo;

use Cms\Classes\Theme;
use Cms\Classes\Controller;
use Media\Classes\MediaLibrary;

class MetaHandler
{
    protected $controller;
    protected $page;
    protected $themeData;
    
    protected $searchTitle;

    public function __construct()
    {
        $this->controller = controller::getController();
        if (!$this->controller) return;
        
        $this->page = $this->controller->getPage();
        $this->themeData = $this->controller->getTheme()->getCustomData();
    }
    
    public function setSearchTitle($title)
    {
        $this->searchTitle = $title;
    }
    
    
    private function getTitleSuffix()
    {
        
    }

}

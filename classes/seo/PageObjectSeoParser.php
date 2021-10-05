<?php

namespace Dynamedia\Posts\Classes\Seo;

use Media\Classes\MediaLibrary;

class PageObjectSeoParser extends Seo
{
    public function __construct($controller, $page, $url)
    {
        $this->controller = $controller;
        $this->page = $page;
        $this->url = $url;
        parent::__construct();
    }


}

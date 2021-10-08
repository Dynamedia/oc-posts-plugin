<?php

namespace Dynamedia\Posts\Classes\Seo;

use Dynamedia\Posts\Models\Tag;

class TagSeoParser extends PostsObjectSeoParser
{
    protected $seo;

    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    public function setProperties()
    {
        parent::setProperties();
    }

    public function setSchema()
    {
        $graph = $this->seo->getSchemaGraph();

        $graph->getWebPage()
            ->setProperty("@id", $this->model->url . "#webpage")
            ->title($this->model->name)
            ->description(strip_tags($this->model->excerpt));

        $graph->getBreadcrumbs()
            ->setProperty("@id", $this->model->url . "#breadcrumbs");

            $graph->addBreadcrumb($this->model->name, $this->model->url);
    }
}

<?php

namespace Dynamedia\Posts\Classes\Seo;

use Dynamedia\Posts\Models\Category;

class CategorySeoParser extends PostsObjectSeoParser
{
    protected $seo;

    public function __construct(Category $category)
    {
        parent::__construct($category);
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

        foreach ($this->model->getCachedPathFromRoot() as $item) {
            $graph->addBreadcrumb($item['name'], $item['url']);
        }
    }
}

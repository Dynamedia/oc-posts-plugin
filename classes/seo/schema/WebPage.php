<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Spatie\SchemaOrg\Schema;


class WebPage
{
    protected $model;
    protected $webPage;

    public function __construct($model)
    {
        $this->model = $model;
        $this->setBaseType();
        $this->setSchema();
    }

    private function setBaseType()
    {
        $this->webPage = Schema::webPage();
    }
    private function setSchema()
    {
            $this->webPage->url($this->model->url)
                ->publisher((new Publisher())->getSchema());
    }

    public function getSchema()
    {
        return $this->webPage;
    }

}

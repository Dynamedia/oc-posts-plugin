<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Spatie\SchemaOrg\Schema;


class BlogPosting extends Article
{
    protected $model;

    public function __construct($model)
    {
        parent::__construct($model);
        $this->setBaseType();
        $this->setSchema();
    }

    private function setBaseType()
    {
        $this->content = Schema::blogPosting();
    }

    public function getSchema()
    {
        return $this->content;
    }

}

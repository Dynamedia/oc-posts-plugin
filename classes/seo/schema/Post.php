<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Dynamedia\Posts\Models\Post as PostModel;


class Post extends WebPage
{
    protected $model;
    protected $content;

    public function __construct(PostModel $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }


    protected function setSchema()
    {
        $this->content->getSchema()
            ->mainEntityOfPage($this->webPage)
            ->publisher((new Publisher())->getSchema());

        if (!empty($this->model->author->profile)) {
            $this->content->author($this->model->author->profile->getSeoSchema());
        }

        if (!empty($this->model->editor->profile)) {
            $this->content->editor($this->model->author->profile->getSeoSchema());
        }

        $about = [];
        if (!empty($this->model->seo['schema_content']['schema_about'])) {
            foreach ($this->model->seo['schema_content']['schema_about'] as $item) {
                $type = $item['_group'];
                $about[] = SchemaFactory::makeNative($type, $this->model, $item)->getSchema();
            }
            if ($about) {
                $this->content->about($about);
            }
        }

        $mentions = [];
        if (!empty($this->model->seo['schema_content']['schema_mentions'])) {
            foreach ($this->model->seo['schema_content']['schema_mentions'] as $item) {
                $type = $item['_group'];
                $mentions[] = SchemaFactory::makeNative($type, $this->model, $item)->getSchema();
            }
            if ($mentions) {
                $this->content->mentions($mentions);
            }
        }
    }

    public function getSchema()
    {
        return $this->content->getSchema();
    }

}

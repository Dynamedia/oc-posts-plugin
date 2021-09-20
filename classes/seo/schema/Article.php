<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Spatie\SchemaOrg\Schema;


class Article extends Post
{
    protected $article;

    public function __construct($model)
    {
        parent::__construct($model);
        $this->setBaseType();
        $this->setSchema();
    }

    private function setBaseType()
    {
        $this->content = Schema::article();
    }

    protected function setSchema()
    {
        $this->content->headline($this->model->title)
            ->dateCreated($this->model->created_at)
            ->url($this->model->url)
            ->abstract(strip_tags($this->model->excerpt));

        if ($this->model->primary_category) {
            $this->content->articleSection($this->model->primary_category->name);
        }

        if ($this->model->is_published && $this->model->published_at) {
            $this->content->datePublished((string) $this->model->published_at);
            if ($this->model->updated_at > $this->model->published_at) {
                $this->content->dateModified((string) $this->model->updated_at);
            }
        }

        if ($this->model->published_until) {
            $this->content->expires((string) $this->model->published_until);
        }

        $imageUrl = $this->model->getBestImage();
        if ($imageUrl) {
            $image = Schema::imageObject()
                ->url(\URL::to(\System\Classes\MediaLibrary::url($imageUrl)));
            $this->content->image($image);
        }





        parent::setSchema();

    }

}

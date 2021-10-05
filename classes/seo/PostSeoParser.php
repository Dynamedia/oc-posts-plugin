<?php

namespace Dynamedia\Posts\Classes\Seo;

use Dynamedia\Posts\Classes\Seo\Schema\SchemaFactory;
use Dynamedia\Posts\Models\Post;

class PostSeoParser extends PostsObjectSeoParser
{
    protected $seo;

    public function __construct(Post $post)
    {
        parent::__construct($post);
    }

    public function setProperties()
    {
        parent::setProperties();
        $this->setSchema();
    }

    public function setSchema()
    {
        $graph = $this->seo->getSchemaGraph();

        // Create the article
        if (!empty($this->model->seo['schema_type'])) {
            $type = $this->model->seo['schema_type'];
        } else {
            $type = 'article';
        }
        $article = SchemaFactory::makeSpatie($type)
            ->setProperty("mainEntityOf", ["@id" => $graph->getWebpageId()])
            ->setProperty("isPartOf", ["@id" => $graph->getWebpageId()]);

        // And the people associated
        $author = !empty($this->model->author->profile) ? $this->model->author->profile->getSeoSchema() : null;
        $editor = !empty($this->model->editor->profile) ? $this->model->editor->profile->getSeoSchema() : null;

        if ($author) {
            $id = $this->model->url . "#author";
            $author->setProperty("@id", $id);
            $graph->set($author, 'author');
            $article->setProperty('author', ["@id" => $id]);
        }

        if ($editor) {
            $id = $this->model->url . "#editor";
            $author->setProperty("@id", $id);
            $graph->set($editor, 'editor');
            $article->setProperty('editor', ["@id" => $id]);
        }

        $article->setProperty("@id", $this->model->url . "#article")
            ->headline($this->model->title)
            ->name($this->model->title)
            ->dateCreated($this->model->created_at)
            ->url($this->model->url)
            ->abstract(strip_tags($this->model->excerpt));

        if ($this->model->primary_category) {
            $article->articleSection($this->model->primary_category->name);
        }

        if ($this->model->is_published && $this->model->published_at) {
            $article->datePublished((string) $this->model->published_at);
            if ($this->model->updated_at > $this->model->published_at) {
                $article->dateModified((string) $this->model->updated_at);
            }
        }

        if ($this->model->published_until) {
            $article->expires((string) $this->model->published_until);
        }

        $imageUrl = $this->model->getBestImage();
        if ($imageUrl) {
            $image = SchemaFactory::makeSpatie('imageObject')
                ->url(\URL::to(\Media\Classes\MediaLibrary::url($imageUrl)));
            $article->image($image);
        }

        // Article is about

        $aboutItems = [];
        $about = !empty($this->model->seo['schema_content']['schema_about']) ? $this->model->seo['schema_content']['schema_about'] : [] ;
        foreach ($about as $item) {
            $thing = SchemaFactory::makeSpatie($item['_group']);
            unset($item["_group"]);
            foreach ($item as $k => $v) {
                $thing->setProperty($k, $v);
            }
            $aboutItems[] = $thing;
        }

        $article->about($aboutItems);

        // Article mentions

        $mentionsItems = [];
        $mentions = !empty($this->model->seo['schema_content']['schema_mentions']) ? $this->model->seo['schema_content']['schema_mentions'] : [] ;
        foreach ($mentions as $item) {
            $thing = SchemaFactory::makeSpatie($item['_group']);
            unset($item["_group"]);
            foreach ($item as $k => $v) {
                $thing->setProperty($k, $v);
            }
            $mentionsItems[] = $thing;
        }

        $article->mentions($mentionsItems);

        $graph->set($article, "article");

        // Update the WebPage

        $graph->getWebPage()
            ->setProperty("@id", $this->model->url . "#webpage")
            ->title($this->model->title)
            ->description(strip_tags($this->model->excerpt));

        $graph->getBreadcrumbs()
            ->setProperty("@id", $this->model->url . "#breadcrumbs");

        if ($this->model->primary_category) {
            foreach ($this->model->primary_category->getCachedPathFromRoot() as $item) {
                $graph->addBreadcrumb($item['name'], $item['url']);
            }
        }
        $graph->addBreadcrumb($this->model->title, $this->model->url);
    }
}

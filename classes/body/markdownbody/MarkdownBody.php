<?php namespace Dynamedia\Posts\Classes\Body\Markdownbody;

use Dynamedia\Posts\Classes\Body\Body;
use Markdown;

class MarkdownBody extends Body
{

    public function __construct($model)
    {
        parent::__construct($model);
    }

    protected function setPages()
    {
        // For now, just explode on a horizontal rule with pagebreak class
        $this->pages = explode('<hr class="pagebreak">', Markdown::parse($this->model->body_document['markdown_body']));
    }

}

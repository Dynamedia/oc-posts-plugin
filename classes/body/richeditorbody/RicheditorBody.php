<?php namespace Dynamedia\Posts\Classes\Body\Richeditorbody;

use Dynamedia\Posts\Classes\Body\Body;

class RicheditorBody extends Body
{

    public function __construct($model)
    {
        parent::__construct($model);
    }

    protected function setPages()
    {
        // Just explode on a horizontal rule with pagebreak class
        $this->pages = explode('<hr class="pagebreak">', $this->model->body_document['richeditor_body']);
    }

}

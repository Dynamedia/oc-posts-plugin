<?php namespace Dynamedia\Posts\Classes\Body\Markdownbody;

use Dynamedia\Posts\Classes\Body\Body;

class MarkdownBody extends Body
{

    public function __construct($bodyDocument)
    {
        // For now, just explode on a horizontal rule with pagebreak class
        $this->pages = explode('<hr class="pagebreak">', $bodyDocument['markdown_body']);
    }

}

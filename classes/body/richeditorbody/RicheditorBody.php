<?php namespace Dynamedia\Posts\Classes\Body\Richeditorbody;

use Dynamedia\Posts\Classes\Body\Body;

class RicheditorBody extends Body
{

    public function __construct($bodyDocument)
    {
        // For now, just explode on a horizontal rule with pagebreak class
        $this->pages = explode('<hr class="pagebreak">', $bodyDocument['richeditor_body']);
    }

}

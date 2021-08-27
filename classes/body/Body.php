<?php namespace Dynamedia\Posts\Classes\Body;

abstract class Body
{
    const classes = [
      'repeater_body' => \Dynamedia\Posts\Classes\Body\Repeaterbody\RepeaterBody::class,
      'richeditor_body' => \Dynamedia\Posts\Classes\Body\Richeditorbody\RicheditorBody::class,
      'markdown_body' => \Dynamedia\Posts\Classes\Body\Markdownbody\MarkdownBody::class
    ];

    protected $pages = [];
    protected $contents = [];

    /**
     * Render all pages as a single page
     * @return mixed
     */
    public function renderAllPages()
    {
        return implode("\n", $this->getPages());
    }

    /**
     * Get the html for the current page
     *
     * @param int $page
     * @return false|string
     */
    public function renderPage($page) {
        $page--;
        try {
            return $this->pages[$page];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Strip everything but the text content of the post.
     *
     * @return string
     */
    public function getTextContent()
    {
        try {
            $decode = strip_tags(str_replace('><', '> <', $this->renderAllPages()));
            $clean = preg_replace('/[^A-Za-z0-9. ]/', ' ', $decode);
            $result = preg_replace('/\s+/', ' ', $clean);
        } catch (\Exception $e) {
            $result = null;
        }
        return $result;
    }

    /**
     * Get an array of html strings for each page in the body
     *
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }

    public static function getBody($bodyDocument)
    {
        if (array_key_exists($bodyDocument['body_type'], self::classes)) {
            $bodyClass = self::classes[$bodyDocument['body_type']];
            return new $bodyClass($bodyDocument);
        }
    }
}

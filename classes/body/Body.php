<?php namespace Dynamedia\Posts\Classes\Body;

use Cache;
use Carbon\Carbon;

abstract class Body
{
    const classes = [
      'repeater_body' => \Dynamedia\Posts\Classes\Body\Repeaterbody\RepeaterBody::class,
      'richeditor_body' => \Dynamedia\Posts\Classes\Body\Richeditorbody\RicheditorBody::class,
      'markdown_body' => \Dynamedia\Posts\Classes\Body\Markdownbody\MarkdownBody::class,
      'template_body' => \Dynamedia\Posts\Classes\Body\Templatebody\TemplateBody::class
    ];

    protected $model;
    protected $cacheKey;

    protected $pages = [];
    protected $contents = [];

    public function __construct($model)
    {
        $this->model = $model;
        $this->cacheKey = $this->model->getBodyCacheKey();
        if (Cache::has($this->cacheKey)) {
            $arr = Cache::get($this->cacheKey);
            $this->pages = $arr['pages'];
            $this->contents = $arr['contents'];
        } else {
            $this->setPages();
            if ($this->pages) {
                $arr = [
                    'pages'    => $this->pages,
                    'contents' => $this->contents
                    ];
                Cache::put($this->cacheKey, $arr, Carbon::now()->addHours(1));
            }
        }
    }

    /**
     * Render all pages as a single page
     * @return mixed
     */
    public function renderAllPages()
    {
        return implode("\n", $this->getPages());
    }

    public function render()
    {
        return $this->renderAllPages();
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

    public function getContentsList($baseUrl)
    {
        foreach ($this->contents as &$item) {
            $item['url'] = $baseUrl . $item['url_params'];
        }
        return $this->contents;
    }

    public static function getBody($model)
    {
        $bodyDocument = $model->body_document;

        if (!empty($bodyDocument['body_type']) && array_key_exists($bodyDocument['body_type'], self::classes)) {
            $bodyClass = self::classes[$bodyDocument['body_type']];
            return new $bodyClass($model);
        } else {
            return new \Dynamedia\Posts\Classes\Body\Repeaterbody\RepeaterBody($model);
        }
    }
}

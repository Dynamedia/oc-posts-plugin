<?php
namespace Dynamedia\Posts\Classes\Seo;

use Cache;
use Dynamedia\Posts\Classes\Seo\Schema\ExtendedGraph;
use Media\Classes\MediaLibrary;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;

class Seo
{
    protected $controller;
    protected $page;
    protected $pageMd5;
    protected $themeData;
    protected $translator;
    protected $cacheKey;
    protected $cached = false;

    protected $schemaGraph;
    protected $url;
    protected $alternativeUrls = [];
    protected $searchTitle;
    protected $searchDescription;
    protected $openGraphTitle;
    protected $openGraphDescription;
    protected $openGraphImage;
    protected $twitterSite;
    protected $twitterCreator;
    protected $twitterTitle;
    protected $twitterDescription;
    protected $twitterImage;
    protected $schemaWebsiteId;
    protected $schemaWebpageId;
    protected $schemaArticleId;
    protected $schemaPublisherId;
    protected $schemaBreadcrumbsId;
    protected $schemaAuthorId;
    protected $schemaJson;
    protected $output;


    public function __construct()
    {
        $this->schemaGraph = new ExtendedGraph();
        $this->translator = Translator::instance();
    }

    public function getSchemaGraph()
    {
        return $this->schemaGraph;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function getPropertiesArray()
    {
        $properties = [
            'url'                   => $this->url,
            'alternativeUrls'       => $this->alternativeUrls,
            'searchTitle'           => $this->searchTitle,
            'searchDescription'     => $this->searchDescription,
            'openGraphTitle'        => $this->openGraphTitle,
            'openGraphDescription'  => $this->openGraphDescription,
            'openGraphImage'        => $this->openGraphImage,
            'twitterSite'           => $this->twitterSite,
            'twitterCreator'        => $this->twitterCreator,
            'twitterTitle'          => $this->twitterTitle,
            'twitterDescription'    => $this->twitterDescription,
            'twitterImage'          => $this->twitterImage,
            'schemaWebsiteId'       => $this->schemaWebsiteId,
            'schemaWebpageId'       => $this->schemaWebpageId,
            'schemaPublisherId'     => $this->schemaPublisherId,
            'schemaBreadcrumbsId'   => $this->schemaBreadcrumbsId,
            'schemaArticleId'       => $this->schemaArticleId,
            'schemaJson'            => $this->schemaGraph->toScript(),
            'output'                => $this->output
        ];
        return $properties;
    }


    /**
     * Load all necessary fallback defaults, build the views and cache the result
     *
     * @param $controller
     */
    public function loadProperties($controller)
    {
        $this->controller = $controller;
        $this->page = $this->controller->getPage();
        $this->url = $controller->currentPageUrl();
        $this->cacheKey = $this->generateCacheKey($this->url);
        $this->pageMd5 = md5(json_encode($controller->getPage()->attributes));
        $this->loadFromCache();

        if (!$this->cached) {
            // Only need themedata if not cached yet
            $this->themeData = $this->controller->getTheme()->getCustomData();
            $this->checkSearchTitle();
            $this->checkSearchDescription();
            $this->checkOpenGraphTitle();
            $this->checkOpenGraphDescription();
            $this->checkOpenGraphImage();
            $this->checkTwitterSite();
            $this->checkTwitterCreator();
            $this->checkTwitterTitle();
            $this->checkTwitterDescription();
            $this->checkTwitterImage();
            $this->checkAlternativeUrls();
            $this->loadGraph();
            $this->loadView();
            $this->setCache();
        }
    }

    private function setCache()
    {
        Cache::put($this->cacheKey, [
            'properties'    => $this->getPropertiesArray(),
            'md5'           => $this->pageMd5,
        ], 3600);
    }

    private function loadFromCache()
    {
        self::invalidateCache('test');
        $cached = Cache::get($this->cacheKey);
        if (!empty($cached['md5']) && $cached['md5'] === $this->pageMd5) {
            foreach ($cached['properties'] as $key => $val) {
                $this->{$key} = $val;
            }
            $this->cached = true;
        } else {
            $this->cached = false;
        }
    }

    public function hasUrlCached($url)
    {
        return Cache::has($this->generateCacheKey($url));
    }

    private function generateCacheKey($url)
    {
        return "seo_{$url}_" . $this->translator->getLocale();
    }

    /**
     * Forget the url in all locales
     *
     * @param $url
     */
    public static function invalidateCache($url)
    {
        foreach (Locale::all() as $locale) {
            $key = "seo_{$url}_" . $locale->code;
                Cache::forget($key);
        }
    }

    /**
     * Ensure webpage is set correctly. Get key cacheable properties from the graph
     */
    private function loadGraph()
    {
        $this->schemaGraph->getWebpage()
            ->setProperty("@id", $this->url . "#wepbage")
            ->url($this->url)
            ->title($this->searchTitle)
            ->description($this->searchDescription);
        $this->schemaWebsiteId = $this->schemaGraph->getWebsiteId();
        $this->schemaWebpageId = $this->schemaGraph->getWebpageId();
        $this->schemaPublisherId = $this->schemaGraph->getPubisherId();
        $this->schemaArticleId = $this->schemaGraph->getArticleId();
        $this->schemaAuthorId = $this->schemaGraph->getAuthorId();
        $this->schemaBreadcrumbsId = $this->schemaGraph->getBreadcrumbsId();
        $this->schemaJson = $this->schemaGraph->toScript();
    }

    private function loadView()
    {
        $this->output = \View::make('dynamedia.posts::seo.head_seo', ['seo' => $this])
            ->render();
    }

    public function getOutput()
    {
        return $this->output;
    }


    public function setSearchTitle($title)
    {
        $this->searchTitle = $title;
    }

    protected function checkSearchTitle()
    {
        if (!$this->searchTitle && $this->page) {
            if (!empty($this->page->attributes['meta_title'])) {
                $this->searchTitle = $this->page->attributes['meta_title'];
            } elseif(!empty($this->page->attributes['title'])) {
                $this->searchTitle = $this->page->attributes['title'];
            }
        }
    }

    public function getSearchTitle()
    {
        return $this->searchTitle;
    }

    public function setSearchDescription($description)
    {
        $this->searchDescription = $description;
    }

    protected function checkSearchDescription()
    {
        if (!$this->searchDescription && $this->page) {
            if (!empty($this->page->attributes['meta_description'])) {
                $this->searchDescription = $this->page->attributes['meta_description'];
            } elseif(!empty($this->page->attributes['title'])) {
                $this->searchDescription = $this->page->attributes['title'];
            }
        }
    }

    public function getSearchDescription()
    {
        return $this->searchDescription;
    }

    public function setOpenGraphTitle($title)
    {
        $this->openGraphTitle = $title;
    }

    protected function checkOpenGraphTitle()
    {
        if (!$this->openGraphTitle) {
            $this->openGraphTitle = $this->searchTitle;
        }
    }

    public function getOpenGraphTitle()
    {
        return $this->openGraphTitle;
    }

    public function setOpenGraphDescription($description)
    {
        $this->openGraphDescription = $description;
    }

    protected function checkOpenGraphDescription()
    {
        if (!$this->openGraphDescription) {
            $this->openGraphDescription = $this->searchDescription;
        }
    }

    public function getOpenGraphDescription()
    {
        return $this->openGraphDescription;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }


    public function getUrl()
    {
        return $this->url;
    }

    public function setAlternativeUrls($urls)
    {
        $this->alternativeUrls = $urls;
    }

    //todo - copy rainlab logic probably
    public function checkAlternativeUrls()
    {
        if (!$this->alternativeUrls){
            $this->alternativeUrls = [];
        }
    }

    public function getAlternativeUrls()
    {
        return $this->alternativeUrls;
    }

    public function setOpenGraphImage($image)
    {
        $this->openGraphImage = $image;
    }

    protected function checkOpenGraphImage()
    {
        if (!$this->openGraphImage) {
            $path = false;
            if (!empty($this->themeData->images['social']['facebook'])) {
                $path = $this->themeData->images['social']['facebook'];
            } elseif (!empty($this->themeData->images['social']['twitter'])) {
                $path = $this->themeData->images['social']['twitter'];
            } elseif (!empty($this->themeData->images['banner']['default'])) {
                $path = $this->themeData->images['banner']['default'];
            }
            if ($path) {
                $this->openGraphImage = MediaLibrary::url($path);
            }
        }
    }

    public function getOpenGraphImage()
    {
        return $this->openGraphImage;
    }

    public function setTwitterSite($handle)
    {
        $this->twitterSite = $handle;
    }

    protected function checkTwitterSite()
    {
        if (!$this->twitterSite) {
            if (!empty($this->themeData->twitter_handle)) {
                $this->twitterSite = $this->themeData->twitter_handle;
            }
        }
    }

    public function getTwitterSite()
    {
        return $this->twitterSite;
    }

    public function setTwitterCreator($handle)
    {
        $this->twitterCreator = $handle;
    }

    protected function checkTwitterCreator()
    {
        if (!$this->twitterCreator) {
            $this->twitterCreator = $this->twitterSite;
        }
    }

    public function getTwitterCreator()
    {
        return $this->twitterCreator;
    }

    public function setTwitterTitle($title)
    {
        $this->twitterTitle = $title;
    }

    protected function checkTwitterTitle()
    {
        if (!$this->twitterTitle) {
            $this->twitterTitle = $this->searchTitle;
        }
    }

    public function getTwitterTitle()
    {
        return $this->twitterTitle;
    }

    public function setTwitterDescription($description)
    {
        $this->twitterDescription = $description;
    }

    protected function checkTwitterDescription()
    {
        if (!$this->twitterDescription) {
            $this->twitterDescription = $this->searchDescription;
        }
    }

    public function getTwitterDescription()
    {
        return $this->twitterDescription;
    }

    public function setTwitterImage($image)
    {
        $this->twitterImage = $image;
    }

    protected function checkTwitterImage()
    {
        if (!$this->twitterImage) {
            $path = false;
            if (!empty($this->themeData->images['social']['twitter'])) {
                $path = $this->themeData->images['social']['twitter'];
            } elseif (!empty($this->themeData->images['social']['facebook'])) {
                $path = $this->themeData->images['social']['facebook'];
            } elseif (!empty($this->themeData->images['banner']['default'])) {
                $path = $this->themeData->images['banner']['default'];
            }
            if ($path) {
                $this->twitterImage = MediaLibrary::url($path);
            }
        }
    }

    public function getTwitterImage()
    {
        return $this->twitterImage;
    }

    public function getSchemaWebsiteId()
    {
        return $this->schemaWebsiteId;
    }

    public function getSchemaWebpageId()
    {
        return $this->schemaWebpageId;
    }

    public function getSchemaArticleId()
    {
        return $this->schemaArticleId;
    }

    public function getSchemaAuthorId()
    {
        return $this->schemaAuthorId;
    }

    public function getSchemaPublisherId()
    {
        return $this->schemaPublisherId;
    }

    public function getSchemaBreadcrumbsId()
    {
        return $this->schemaBreadcrumbsId;
    }

    public function getThemeData()
    {
        return $this->themeData->attributes;
    }
}

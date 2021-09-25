<?php

namespace Dynamedia\Posts\Classes\Seo\Schema;

use Cms\Classes\Theme;
use RainLab\Translate\Classes\Translator;
use Spatie\SchemaOrg\Graph;

use Url;

class ExtendedGraph extends Graph
{
    protected $baseUrl;
    protected $translator;

    protected $postTypes = [
        \Spatie\SchemaOrg\Article::class,
        \Spatie\SchemaOrg\BlogPosting::class,
        \Spatie\SchemaOrg\NewsArticle::class,
        \Spatie\SchemaOrg\TechArticle::class
    ];

    public function __construct(string $context = null)
    {
        parent::__construct($context);

        $this->translator = Translator::instance();
        $this->baseUrl = $this->getBaseUrl();
        $this->setPublisher();
        $this->setWebsite();
    }

    public function getBaseUrl()
    {
        if ($this->baseUrl) return $this->baseUrl;

        $parts = parse_url(Url::to('/'));
        $translatedUrl = http_build_url($parts, [
            'path' => '/' . Translator::instance()->getPathInLocale('/', $this->translator->getLocale())
        ]);

        return $translatedUrl;
    }

    public function getPubisherId()
    {
        return "{$this->baseUrl}#publisher";
    }

    public function getWebsiteId()
    {
        return "{$this->baseUrl}#website";
    }

    public function getWebpageId()
    {
        return "{$this->baseUrl}#webpage";
    }

    protected function setPublisher()
    {
        $themeSettings = Theme::getActiveTheme()->operator;
        $typeOption = !empty($themeSettings['operator_type']) ? $themeSettings['operator_type'] : 'organization';
        $publisher = SchemaFactory::makeSpatie($typeOption)
            ->setProperty('@id', $this->getPubisherId());

        $publisherAddress = SchemaFactory::makeSpatie('postalAddress')
            ->addressCountry($themeSettings['address_country'])
            ->addressLocality($themeSettings['address_city'])
            ->addressRegion($themeSettings['address_region'])
            ->postalCode($themeSettings['address_region'])
            ->streetAddress($themeSettings['address_street']);

        $publisherGeo = SchemaFactory::makeSpatie('geoCoordinates')
            ->latitude($themeSettings['address_latitude'])
            ->longitude($themeSettings['address_longitude']);

        $publisher->name($themeSettings['name'])
            ->address($publisherAddress)
            ->geo($publisherGeo)
            ->url($this->getBaseUrl());

        $this->add($publisher, "publisher");
    }

    protected function setWebsite()
    {
        $website = SchemaFactory::makeSpatie('WebSite')
            ->setProperty('@id', $this->getWebsiteId())
            ->publisher(['@id' => $this->getPubisherId()]);

        $this->add($website, "website");
    }

    public function getPublisher()
    {
        $publisher = null;

        try {
            $publisher = $this->get('Spatie\SchemaOrg\Organization', 'publisher');
        } catch (\Exception $e) {
            $publisher = $this->get('Spatie\SchemaOrg\Person', 'publisher');
        } finally {
            $publisher = SchemaFactory::makeSpatie('Organization')
                ->setProperty("@id", $this->getPubisherId());
        }

        return $publisher;
    }

    public function getWebsite()
    {
        return $this->get('Spatie\SchemaOrg\WebSite', 'website');
    }

    public function getArticle()
    {
        foreach ($this->postTypes as $postType) {
            try {
                return $this->get($postType, "article");
            } catch (\Exception $e) {
                continue;
            }
        }
        return SchemaFactory::makeSpatie('Article');
    }

}

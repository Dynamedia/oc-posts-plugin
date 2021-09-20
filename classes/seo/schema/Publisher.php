<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Dynamedia\Posts\Models\Settings;
use Spatie\SchemaOrg\Schema;

class Publisher
{
    protected $publisher;
    private $settings;

    public function __construct()
    {
        $this->settings = Settings::instance();
        $this->setBaseType();
        $this->setSchema();
    }

    private function setBaseType()
    {
        $type = $this->settings->get('publisherType');
        if ($type) {
            $this->publisher = SchemaFactory::makeSpatie($type);
        } else {
            $this->publisher = SchemaFactory::makeSpatie('organization');
        }
    }

    private function setSchema()
    {
        $this->publisher->name($this->settings->get('publisherName'));

        if ($this->settings->get('publisherUrl')) {
            $this->publisher->url($this->settings->get('publisherUrl'));
        } else {
            $this->publisher->url(\URL::to('/'));
        }

        if ($this->settings->get('publisherLogo')) {
            $logo = Schema::imageObject()
                ->url(\URL::to(\System\Classes\MediaLibrary::url($this->settings->get('publisherLogo'))))
                ->caption($this->settings->get('publisherName'));
            $this->publisher->logo($logo);
        }
    }

    public function getSchema()
    {
        return $this->publisher;
    }

}

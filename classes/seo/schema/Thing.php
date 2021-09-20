<?php
namespace Dynamedia\Posts\Classes\Seo\Schema;

use Spatie\SchemaOrg\Schema;


class Thing
{
    protected $data;
    protected $thing;

    public function __construct($model, $repeaterData)
    {
        $this->data = $repeaterData;
        $this->setBaseType();
        $this->setSchema();
    }

    private function setBaseType()
    {
        $this->thing = Schema::thing();
    }

    private function setSchema()
    {
        if ($this->getData('name')) {
            $this->thing->name($this->getData('name'));
        }
        if ($this->getData('description')) {
            $this->thing->description($this->getData('description'));
        }
        if ($this->getData('sameAs')) {
            $this->thing->sameAs($this->getData('sameAs'));
        }
        if ($this->getData('image')) {
            $image = Schema::imageObject()
                ->url(\URL::to(\System\Classes\MediaLibrary::url($this->getData('image'))));
            $this->thing->image($image);
        }
        if ($this->getData('sameAs')) {
            $this->thing->sameAs($this->getData('sameAs'));
        }
    }

    public function getSchema()
    {
        return $this->thing;
    }

    private function getData($key) {
        if (!empty($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

}

<?php

namespace Dynamedia\Posts\Classes\Extenders;
use Event;

class ExtendThemeFormConfig
{
    public function subscribe()
    {
        Event::listen('cms.theme.extendFormConfig', function ($themeCode, &$config) {
            array_set($config, 'tabs.fields.site_brand', [
                'label'       => 'Site Brand',
                'type'        => 'text',
                'tab'         => 'Branding'
                ]);

            array_set($config, 'tabs.fields.site_name', [
                'label'       => 'Site Name',
                'type'        => 'text',
                'tab'         => 'Branding'
            ]);

            array_set($config, 'tabs.fields.site_description', [
                'label'       => 'Site Description',
                'type'        => 'text',
                'tab'         => 'Branding'
            ]);

            array_set($config, 'tabs.fields.facebook_url', [
                'label'       => 'Facebook URL',
                'type'        => 'text',
                'placeholder' => 'https://facebook.com/dynamediaUK',
                'tab'         => 'Social'
            ]);

            array_set($config, 'tabs.fields.facebook_app_id', [
                'label'       => 'Facebook App ID',
                'type'        => 'text',
                'placeholder' => '01235456789',
                'tab'         => 'Social'
            ]);

            array_set($config, 'tabs.fields.twitter_handle', [
                'label'       => 'Twitter Handle',
                'type'        => 'text',
                'placeholder' => '@DynamediaUK',
                'tab'         => 'Social'
            ]);


            array_set($config, 'tabs.fields.images', [
                'type'        => 'nestedform',
                'tab'         => 'Images',
                'form'        => plugins_path('/dynamedia/posts/config/forms/image/theme.yaml')
            ]);
        });
    }
}

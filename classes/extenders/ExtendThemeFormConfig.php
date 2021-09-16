<?php

namespace Dynamedia\Posts\Classes\Extenders;
use Cms\Models\ThemeData;
use Dynamedia\Posts\Controllers\Posts;
use Dynamedia\Posts\Models\Post;
use Event;

class ExtendThemeFormConfig
{
    public function subscribe()
    {
        ThemeData::extend(function ($model) {
            $model->addJsonable('images');
        });

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

            array_set($config, 'tabs.fields.title_append', [
                'label'       => 'Append to  Title',
                'tab'         => 'Branding',
                'type'        => 'dropdown',
                'options'     => [
                    'none'        => 'None',
                    'site_name'   => 'Site Name',
                    'site_brand'  => 'Site Brand',
                    'both'        => 'Site Name & Brand',
                ],
                'default' => 'name'
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
                'type'              => 'nestedform',
                'tab'               => 'Images',
                'form'              => plugins_path('/dynamedia/posts/config/forms/image/theme.yaml')
            ]);
        });

    }


}

<?php

namespace Dynamedia\Posts\Classes\Extenders;
use Cms\Models\ThemeData;
use Lang;
use Event;

class ExtendThemeFormConfig
{
    public function subscribe()
    {
        ThemeData::extend(function ($model) {
            $model->addJsonable('images', 'operator');
            $translatable = [
                'site_brand',
                'site_name',
                'site_description',
            ];
            if (!empty($model->translatable)) {
                $model->translatable = array_merge($this->translatable, $translatable);
            } else {
                $model->translatable = $translatable;
            }
        });


        Event::listen('cms.theme.extendFormConfig', function ($themeCode, &$config) {
            array_set($config, 'tabs.fields.site_brand', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.site_brand'),
                'type'        => 'text',
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.branding')
            ]);

            array_set($config, 'tabs.fields.site_name', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.site_name'),
                'type'        => 'text',
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.branding')
            ]);

            array_set($config, 'tabs.fields.site_description', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.site_description'),
                'type'        => 'text',
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.branding')
            ]);

            array_set($config, 'tabs.fields.title_append', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.append_to_title'),
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.branding'),
                'type'        => 'dropdown',
                'options'     => [
                    'none'        => Lang::get('dynamedia.posts::lang.theme_form_config.options.append_title_none'),
                    'site_name'   => Lang::get('dynamedia.posts::lang.theme_form_config.options.append_title_name'),
                    'site_brand'  => Lang::get('dynamedia.posts::lang.theme_form_config.options.append_title_brand'),
                    'both'        => Lang::get('dynamedia.posts::lang.theme_form_config.options.append_title_name_brand'),
                ],
                'default' => 'site_name'
            ]);

            array_set($config, 'tabs.fields.facebook_url', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.facebook_url'),
                'type'        => 'text',
                'placeholder' => Lang::get('dynamedia.posts::lang.theme_form_config.placeholders.facebook_url'),
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.social')
            ]);

            array_set($config, 'tabs.fields.facebook_app_id', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.facebook_app_id'),
                'type'        => 'text',
                'placeholder' => Lang::get('dynamedia.posts::lang.theme_form_config.placeholders.facebook_app_id'),
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.social')
            ]);

            array_set($config, 'tabs.fields.twitter_handle', [
                'label'       => Lang::get('dynamedia.posts::lang.theme_form_config.labels.twitter_handle'),
                'type'        => 'text',
                'placeholder' => Lang::get('dynamedia.posts::lang.theme_form_config.placeholders.twitter_handle'),
                'tab'         => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.social')
            ]);


            array_set($config, 'tabs.fields.images', [
                'type'              => 'nestedform',
                'tab'               => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.images'),
                'form'              => plugins_path('/dynamedia/posts/config/forms/theme/image.yaml')
            ]);

            array_set($config, 'tabs.fields.operator', [
                'type'              => 'nestedform',
                'tab'               => Lang::get('dynamedia.posts::lang.theme_form_config.tabs.site_operator'),
                'form'              => plugins_path('/dynamedia/posts/config/forms/theme/operator.yaml')
            ]);
        });
    }
}

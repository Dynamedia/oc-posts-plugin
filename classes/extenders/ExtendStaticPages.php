<?php

namespace Dynamedia\Posts\Classes\Extenders;

use Event;
use Yaml;
use File;
use Dynamedia\Posts\Classes\Seo\StaticPagesSeoParser;

class ExtendStaticPages
{
    public function subscribe()
    {
        \RainLab\Pages\Classes\Page::extend(function ($model) {
            $model->translatable = array_merge($model->translatable, [
                'viewBag[banner_image_alt]',
                'viewBag[seo][about]',
                'viewBag[seo][keywords]',
                'viewBag[seo][search_description]',
                'viewBag[seo][opengraph_title]',
                'viewBag[seo][opengraph_description]',
                'viewBag[seo][twitter_title]',
                'viewBag[seo][twitter_description]',
            ]);

            $model->addDynamicMethod('getHtmlHeadAttribute', function() use($model) {
                $seoData = new StaticPagesSeoParser($model);
                return \View::make('dynamedia.posts::seo.head_seo', [
                    'search' => $seoData->getSearchArray(),
                    'openGraph' => $seoData->getOpenGraphArray(),
                    'twitter' => $seoData->getTwitterArray(),
                    'schema' => $seoData->getSchemaArray(),
                    'themeData' => $seoData->getThemeData()
                ]);
            });

//            $model->addDynamicMethod('setSeoSchemaAttribute', function($value) use($model) {
//                 $model->seo_schema = $value;
//            });
        });

        Event::listen('cms.page.beforeDisplay', function($controller, $url, $page) {
//            $staticPage = false;
//            if (!empty($page->apiBag['staticPage'])) {
//                $staticPage = $page->apiBag['staticPage'];
//            }
//            if (!$staticPage) return;
//            dd($staticPage->html_head);
////
////            $seoParser = new StaticPagesSeoParser($staticPage);
////            $staticPage->seo_schema = $seoParser->getSchemaArray();
        });

        Event::listen('backend.form.extendFieldsBefore', function ($widget) {

            // Only for the Page model
            if (!$widget->model instanceof \RainLab\Pages\Classes\Page) {
                return;
            }

            if ($widget->isNested) {
                return;
            }

            $bannerImageFile = plugins_path('dynamedia/posts/config/forms/image/banner_static_pages.yaml');
            $bannerConfig = Yaml::parse(File::get($bannerImageFile));
            $widget->tabs['fields'] = $widget->tabs['fields'] + $bannerConfig;

            $socialImageFile = plugins_path('dynamedia/posts/config/forms/image/social_static_pages.yaml');
            $socialConfig = Yaml::parse(File::get($socialImageFile));
            $widget->tabs['fields'] = $widget->tabs['fields'] + $socialConfig;

            $seoFile = plugins_path('dynamedia/posts/config/forms/seo_static_pages.yaml');
            $seoConfig = Yaml::parse(File::get($seoFile));
            $widget->tabs['fields'] = $widget->tabs['fields'] + $seoConfig;


        });
    }
}

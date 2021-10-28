<?php namespace Dynamedia\Posts\Classes\Listeners;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Body\Templatebody\TemplateBody;
use Dynamedia\Posts\Models\PostTranslation;
use Dynamedia\Posts\Models\Post;
use Cache;

class PostsController
{
    public function subscribe($event)
    {
        $event->listen('backend.form.extendFields', function ($widget) {

            if (!$widget->model instanceof Post) {
                return;
            }

            if ($widget->arrayName === "Post[body_document][template_body]") {
                $filePath = null;

                if (!empty($widget->model->body_document['template_body_options'])) {
                    $filePath = TemplateBody::getYamlFilePath($widget->model->body_document['template_body_options']);
                }

                $vars = post('Post');

                if (!empty($vars['body_document']['template_body_options'])) {
                    $filePath = TemplateBody::getYamlFilePath($vars['body_document']['template_body_options']);
                }

                if ($filePath) {
                    $cacheKey = TemplateBody::getConfigCacheKey($filePath);
                    if (Cache::has($cacheKey)) {
                        $config = Cache::get($cacheKey);
                    } else {
                        $config = TemplateBody::parseConfig($filePath);
                        if (config('app.debug') == false) {
                            Cache::forever($cacheKey, $config);
                        }
                    }

                    if (!empty($config['fields'])) {
                        $widget->addFields($config['fields']);
                    } elseif (!empty($config['tabs']['fields'])) {
                        $widget->addTabFields($config['tabs']['fields']);
                    }
                }
            }
        });

        $event->listen('backend.form.extendFields', function ($widget) {

            if (!$widget->model instanceof PostTranslation) {
                return;
            }

            if ($widget->arrayName === "PostTranslation[body_document][template_body]") {
                $filePath = null;

                if (!empty($widget->model->body_document['template_body_options'])) {
                    $filePath = TemplateBody::getYamlFilePath($widget->model->body_document['template_body_options']);
                }

                $vars = post('PostTranslation');

                if (!empty($vars['body_document']['template_body_options'])) {
                    $filePath = TemplateBody::getYamlFilePath($vars['body_document']['template_body_options']);
                }

                if ($filePath) {
                    $cacheKey = TemplateBody::getConfigCacheKey($filePath);
                    if (Cache::has($cacheKey)) {
                        $config = Cache::get($cacheKey);
                    } else {
                        $config = TemplateBody::parseConfig($filePath);
                        if (config('app.debug') == false) {
                            Cache::forever($cacheKey, $config);
                        }
                    }

                    if (!empty($config['fields'])) {
                        $widget->addFields($config['fields']);
                    } elseif (!empty($config['tabs']['fields'])) {
                        $widget->addTabFields($config['tabs']['fields']);
                    }
                }
            }
        });

    }
}

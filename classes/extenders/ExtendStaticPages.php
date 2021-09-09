<?php

namespace Dynamedia\Posts\Classes\Extenders;

use Event;

class ExtendStaticPages
{
    public function subscribe()
    {
        Event::listen('backend.form.extendFields', function($widget) {

            // Only for the Page model
            if (!$widget->model instanceof \RainLab\Pages\Classes\Page) {
                return;
            }

            $widget->removeField('viewBag[meta_title]');
            $widget->removeField('viewBag[meta_description]');
            //$widget->addTabFields([
            //    'viewBag[images]' => [
            //        'type'    => 'nestedform',
            //        'tab'     => 'Images',
            //        'form'    => plugins_path('/dynamedia/posts/config/forms/image/theme.yaml')
            //    ],
            //]);

        });
    }
}

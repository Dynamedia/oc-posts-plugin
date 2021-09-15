<?php namespace Dynamedia\Posts\Traits;
use Cms\Classes\Controller;
use Cms\Classes\Theme;

Trait ControllerTrait {

    private function getController()
    {
        $controller = Controller::getController();
        if (!$controller) {
            $controller = new Controller(Theme::getActiveTheme());
        }
        return $controller;
    }

}

<?php
namespace Dynamedia\Posts\Classes\Helpers;
use Config;
use Lang;
use Cms\Classes\Theme;

class ThemeHelper
{

    /**
     * Allows the theme.yaml to define a css file path using postsBackendCss attribute
     *
     * @return string
     */
    public static function getBackendCss()
    {
        if (!empty(Theme::getActiveTheme()->getConfig()['postsBackendCss'])) {
            return Theme::getActiveTheme()->getConfig()['postsBackendCss'];
        } else {
            return "/plugins/dynamedia/posts/assets/css/posts-preview.css";
        }
    }
}

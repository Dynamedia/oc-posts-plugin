<?php namespace Dynamedia\Posts\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Lang;

class AboutPosts extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'aboutposts');
        $this->pageTitle = Lang::get('dynamedia.posts::lang.aboutposts.page_title');

        /*
        *************************************************************************
        This is very simple by design - All source code is available to view
        at github and no attempt to obfuscate code has been or will be made.
        However, the license is legally binding and you should only use the code
        in accordance with the license.
        Please support development buy purchasing a license if you use this
        plugin for commercial/monetary gain. Thanks, Rob (Dynamedia Limited)
        *************************************************************************
        */

        if (class_exists('Dynamedia\PostsCommercialLicense\Plugin')) {
            $this->vars['commercial_license'] = true;
        } else {
            $this->vars['commercial_license'] = false;
        }
    }
}

<?php namespace Dynamedia\Posts\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Dynamedia\Posts\Classes\Helpers\ThemeHelper;

/**
 * Post Slugs Backend Controller
 */
class PostSlugs extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'dynamedia.posts.manage_slugs'
    ];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->addCss("/plugins/dynamedia/posts/assets/css/posts-backend-style.css", "1.0.0");
        $this->addCss(ThemeHelper::getBackendCss(), "1.0.0");
        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'postslugs');
    }
}

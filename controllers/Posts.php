<?php namespace Dynamedia\Posts\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Posts Back-end Controller
 */
class Posts extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $requiredPermissions = [
        'dynamedia.posts.access_plugin'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `RelationController` behavior.
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'posts');
    }

    public function listExtendQuery($query)
    {
        $query->with('author.profile','editor.profile');
        return $query;
    }


}

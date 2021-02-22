<?php namespace Dynamedia\Posts\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Input;
use Dynamedia\Posts\Models\Tag;

/**
 * Tags Back-end Controller
 */
class Tags extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $requiredPermissions = [
        'dynamedia.posts.view_tags',
        'dynamedia.posts.manage_tags'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'tags');
    }


    public function onApprove()
    {
        if (Input::get('checked')) {
            $tags = Tag::whereIn('id', Input::get('checked'))->get();
            foreach ($tags as $tag) {
                $tag->is_approved = true;
                $tag->save();
            }
            return $this->listRefresh();
        }
    }
}

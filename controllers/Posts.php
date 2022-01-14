<?php namespace Dynamedia\Posts\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Dynamedia\Posts\Classes\Helpers\ThemeHelper;
use Dynamedia\Posts\Models\PostTranslation;
use Dynamedia\Posts\Models\Profile;
use Lang;

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

        $this->addCss("/plugins/dynamedia/posts/assets/css/posts-backend-style.css", "1.0.0");
        $this->addCss(ThemeHelper::getBackendCss(), "1.0.0");
        BackendMenu::setContext('Dynamedia.Posts', 'posts', 'posts');
    }

    public function listExtendQuery($query)
    {
        $query->with('author.profile','editor.profile');
        return $query;
    }


    public function listFilterExtendScopes($filter)
    {
        $filter->addScopes([
            'author_by_profile' => [
                'label' => Lang::get('dynamedia.posts::lang.posts.labels.author'),
                'modelClass' => 'Dynamedia\Posts\Models\Profile',
                'scope' => 'applyWhereAuthor',
                //'default' => [\BackendAuth::getUser()->profile->id],
                'nameFrom' => 'fullName',
                'valueFrom' => 'id'
            ],
            'editor_by_profile' => [
                'label' => Lang::get('dynamedia.posts::lang.posts.labels.editor'),
                'modelClass' => 'Dynamedia\Posts\Models\Profile',
                'scope' => 'applyWhereEditor',
                //'default' => [\BackendAuth::getUser()->profile->id],
                'nameFrom' => 'fullName',
                'valueFrom' => 'id'
            ],
        ]);
    }

    public function update_onSave($recordId = null, $context = null)
    {
        parent::update_onSave($recordId, $context);
        // Update some form fields on save
        //return ['#Form-field-Post-body-group' => $this->formGetWidget()->renderField('body', ['useContainer' => false])];
    }

}

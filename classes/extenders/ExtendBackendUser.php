<?php namespace Dynamedia\Posts\Classes\Extenders;

use Backend\Controllers\Users as BackendUserController;
use Backend\Models\User as BackendUserModel;
use Dynamedia\Posts\Models\Profile;
Use Lang;

class ExtendBackendUser
{
    public function subscribe($event)
    {
        BackendUserModel::extend(function ($model) {
            $model->hasOne['profile'] = [
                'Dynamedia\Posts\Models\Profile',
                'table' => 'dynamedia_posts_profiles'
            ];
            $model->bindEvent('model.afterSave', function() use ($model) {
                if (!$model->exists) {
                    return;
                }
                Profile::getFromUser($model);
            });
        });

        // Ensure users always have profiles
        $event->listen('backend.page.beforeDisplay', function ($backendController, $action, $params) {
            $user = \BackendAuth::getUser();
            if ($user && !$user->profile) {
                Profile::getFromUser($user);
            }
        });


        BackendUserController::extendFormFields(function ($form, $model, $context) {
            if (!$model instanceof BackendUserModel) {
                return;
            }
            if (!$model->exists) {
                return;
            }

            Profile::getFromUser($model);

            $form->addTabFields([
                'profile[username]' => [
                    'label'     => Lang::get('dynamedia.posts::lang.backend_user.labels.username'),
                    'tab'       => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'required'  => true,
                ],
                'profile[twitter_handle]' => [
                    'label'         => Lang::get('dynamedia.posts::lang.backend_user.labels.twitter_handle'),
                    'tab'           => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'placeholder'   => Lang::get('dynamedia.posts::lang.backend_user.placeholders.at_handle')
                ],
                'profile[instagram_handle]' => [
                    'label'         => Lang::get('dynamedia.posts::lang.backend_user.labels.instagram_handle'),
                    'tab'           => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'placeholder'   => Lang::get('dynamedia.posts::lang.backend_user.placeholders.at_handle')
                ],
                'profile[facebook_handle]' => [
                    'label'         => Lang::get('dynamedia.posts::lang.backend_user.labels.facebook_handle'),
                    'tab'           => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'placeholder'   => Lang::get('dynamedia.posts::lang.backend_user.placeholders.handle')
                ],
                'profile[website_url]' => [
                    'label'         => Lang::get('dynamedia.posts::lang.backend_user.labels.website_url'),
                    'tab'           => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'placeholder'   => Lang::get('dynamedia.posts::lang.backend_user.placeholders.website')
                ],
                'profile[mini_biography]' => [
                    'label' => Lang::get('dynamedia.posts::lang.backend_user.labels.mini_biography'),
                    'tab'   => Lang::get('dynamedia.posts::lang.backend_user.tabs.profile'),
                    'type'  => 'richeditor',
                ],
                'profile[full_biography]' => [
                    'label' => Lang::get('dynamedia.posts::lang.backend_user.labels.full_biography'),
                    'tab'   => Lang::get('dynamedia.posts::lang.backend_user.tabs.biography'),
                    'type'  => 'richeditor',
                    'size'  => 'huge',
                ]
            ]);
        });
    }
}



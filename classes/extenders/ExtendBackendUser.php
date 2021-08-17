<?php namespace Dynamedia\Posts\Classes\Extenders;


use Backend\Controllers\Users as BackendUserController;
use Backend\Models\User as BackendUserModel;
use Dynamedia\Posts\Models\Profile;

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
                    'label' => 'Username',
                    'tab' => 'Profile',
                    'required' => true,
                ],
                'profile[twitter_handle]' => [
                    'label' => 'Twitter Username',
                    'tab' => 'Profile',
                    'placeholder' => "@yourUsername"
                ],
                'profile[instagram_handle]' => [
                    'label' => 'Instagram Username',
                    'tab' => 'Profile',
                    'placeholder' => "@yourUsername"
                ],
                'profile[facebook_handle]' => [
                    'label' => 'Facebook Username',
                    'tab' => 'Profile',
                    'placeholder' => "yourUsername"
                ],
                'profile[website_url]' => [
                    'label' => 'Website URL',
                    'tab' => 'Profile',
                    'placeholder' => "https://yourwebsite.com"
                ],
                'profile[mini_biography]' => [
                    'label' => 'Mini Biography',
                    'tab' => 'Profile',
                    'type' => 'richeditor',
                ],
                'profile[full_biography]' => [
                    'label' => 'Full Biography',
                    'tab' => 'Biography',
                    'type' => 'richeditor',
                    'size' => 'huge',
                ]
            ]);
        });
    }
}



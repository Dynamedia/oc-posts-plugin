<?php namespace Dynamedia\Posts\Classes\Extenders;


use Backend\Controllers\Users as BackendUserController;
use Backend\Models\User as BackendUserModel;
use Dynamedia\Posts\Models\Profile;

class ExtendBackendUser
{
    public function subscribe($event)
    {
        BackendUserModel::extend(function ($model) {
            $model->addHidden('login', 'permissions', 'is_superuser', 'role_id', 'is_activated', 'activated_at', 'created_at', 'updated_at', 'deleted_at');
            $model->hasOne['profile'] = [
                'Dynamedia\Posts\Models\Profile',
                'table' => 'dynamedia_posts_profiles'
            ];
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


//        $event->listen('pages.menuitem.listTypes', function() {
//            return [
//                'posts-category'       => 'Posts: A Category',
//                'posts-all-categories' => 'Posts: All Categories',
//                'posts-tag'            => 'Posts: A Tag',
//                'posts-all-tags'       => 'Posts: All Tags',
//                'posts-post'           => 'Posts: A Post',
//                'posts-all-posts'      => 'Posts: All Posts',
//                'posts-category-posts' => 'Posts: All Posts From Category',
//                'posts-tag-posts'      => 'Posts: All Posts With Tag',
//            ];
//        });
//      }


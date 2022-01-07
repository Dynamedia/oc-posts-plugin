<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;
use Lang;

class CategoryAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.category.saving', function($category, $user)  {
            if (!$this->userCanManageCategories($user)) {
                throw new ValidationException([
                    'error' => Lang::get('dynamedia.posts::lang.acl.error.manage_categories')
                ]);
            }
        });


        $event->listen('dynamedia.posts.category.deleting', function($post, $user) {
            //
        });
    }


    private function userCanViewCategories($user)
    {
        return AccessControl::userCanViewCategories($user);
    }

    private function userCanManageCategories($user)
    {
        return AccessControl::userCanManageCategories($user);
    }
}

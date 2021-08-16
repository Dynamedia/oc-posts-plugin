<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;

class CategoryAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.category.saving', function($category, $user)  {
            if (!$this->userCanManageCategories($user)) {
                throw new ValidationException([
                    'error' => "Insufficient permissions to manage categories"
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

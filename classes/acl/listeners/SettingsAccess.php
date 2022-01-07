<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;
use Lang;

class SettingsAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.settings.saving', function($category, $user)  {
            if (!$this->userCanManageSettings($user)) {
                throw new ValidationException([
                    'error' => Lang::get('dynamedia.posts::lang.acl.error.manage_settings')
                ]);
            }
        });
    }


    private function userCanViewSettings($user)
    {
        return AccessControl::userCanViewSettingss($user);
    }

    private function userCanManageSettings($user)
    {
        return AccessControl::userCanManageSettings($user);
    }
}

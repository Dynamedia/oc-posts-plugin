<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;

class SettingsAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.settings.saving', function($category, $user)  {
            if (!$this->userCanManageSettings($user)) {
                throw new ValidationException([
                    'error' => "Insufficient permissions to manage settings"
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

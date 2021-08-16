<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Classes\Acl\Listeners\PostAccess;
use Dynamedia\Posts\Classes\Acl\Listeners\CategoryAccess;
use Dynamedia\Posts\Classes\Acl\Listeners\TagAccess;
use Dynamedia\Posts\Classes\Acl\Listeners\SettingsAccess;
use Event;

class AccessControl
{
    public function subscribe($event)
    {
        // Do not apply ACL to console actions
        if (app()->runningInConsole()) return;

        Event::subscribe(PostAccess::class);
        Event::subscribe(CategoryAccess::class);
        Event::subscribe(TagAccess::class);
        Event::subscribe(SettingsAccess::class);
    }
}

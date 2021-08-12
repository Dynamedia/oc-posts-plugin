<?php namespace Dynamedia\Posts\Classes\Listeners;
use Event;
use Dynamedia\Posts\Classes\Acl\Listeners\PostAccess;

class AccessControl
{
    public function subscribe($event)
    {
        // Do not apply ACL to console actions
        if (app()->runningInConsole()) return;

        Event::subscribe(PostAccess::class);
    }
}

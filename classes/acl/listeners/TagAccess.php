<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;
use Lang;

class TagAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.tag.saving', function($tag, $user)  {
            // Can't edit existing tags
            if (!$this->userCanManageTags($user) && $tag->exists) {
                throw new ValidationException([
                    'error' => Lang::get('dynamedia.posts::lang.acl.error.edit_tag', ['tag' => $tag->name])
                ]);
            }

            // Tag managers get auto approval on new tags
            if (!$tag->exists && $this->userCanManageTags($user)) {
                $tag->is_approved = true;
            } elseif (!$tag->exists) {
                $tag->is_approved = false;
            }
        });

        $event->listen('dynamedia.posts.tag.deleting', function($tag, $user) {
            if (!$this->userCanManageTags($user)) {
                throw new ValidationException([
                    'error' => Lang::get('dynamedia.posts::lang.acl.error.delete_tag', ['tag' => $tag->name])
                ]);
            }
        });
    }


    private function userCanViewTags($user)
    {
        return AccessControl::userCanViewTags($user);
    }

    private function userCanManageTags($user)
    {
        return AccessControl::userCanManageTags($user);
    }
}

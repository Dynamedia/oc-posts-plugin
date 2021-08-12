<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use BackendAuth;
use Dynamedia\Posts\Classes\Acl\AccessControl;
use Dynamedia\Posts\Models\Post;
use Event;

class PostAccess
{
    public $user;

    public function subscribe($event)
    {
        $event->listen('dynamedia.posts.saving', function($post, $user)  {
            if (!$this->userCanEdit($post, $user)) {
                throw new \ValidationException([
                    'error' => "Insufficient permissions to edit {$post->slug}"
                ]);
            }

            if ($post->isDirty('is_published')) {
                if ($post->is_published && !$this->userCanPublish($post, $user)) {
                    throw new ValidationException([
                        'error' => "Insufficient permissions to publish {$post->slug}"
                    ]);
                }
                if (!$post->is_published && !$this->userCanUnpublish($post, $user)) {
                    throw new ValidationException([
                        'error' => "Insufficient permissions to unpublish {$post->slug}"
                    ]);
                }
            }

        });
    }


    private function userCanEdit($post, $user)
    {
        return AccessControl::userCanEditPost($post, $user);
    }

    private function userCanPublish($post, $user)
    {
        return AccessControl::userCanPublishPost($post, $user);
    }

    private function userCanUnPublish($post, $user)
    {
        return AccessControl::userCanUnPublishPost($post, $user);
    }
}

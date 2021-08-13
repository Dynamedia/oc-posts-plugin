<?php namespace Dynamedia\Posts\Classes\Acl\Listeners;

use Dynamedia\Posts\Classes\Acl\AccessControl;
use ValidationException;

class PostAccess
{
    public $user;

    public function subscribe($event)
    {

        $event->listen('dynamedia.posts.post.saving', function($post, $user)  {
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

        $event->listen('dynamedia.posts.post.deleting', function($post, $user) {
            if (!$this->userCanDelete($post, $user)) {
                throw new ValidationException([
                    'error' => "Insufficient permissions to delete {$post->slug}"
                ]);
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

    private function userCanDelete($post, $user)
    {
        return AccessControl::userCanDeletePost($post, $user);
    }
}

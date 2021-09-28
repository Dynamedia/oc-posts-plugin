<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\TagSlug;
use Str;
use ValidationException;

class TagTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.tagtranslation.saving', function ($tagTranslation, $user) {

        });

        // Before Save
        $event->listen('dynamedia.posts.tagtranslation.saving', function ($tagTranslation, $user) {

        });

        // After Save
        $event->listen('dynamedia.posts.tagtranslation.saved', function ($tagTranslation, $user) {

        });

        // Before Delete
        $event->listen('dynamedia.posts.tagtranslation.deleting', function ($tagTranslation, $user) {

        });

        // After Delete
        $event->listen('dynamedia.posts.tagtranslation.deleted', function ($tagTranslation, $user) {

        });
    }
}

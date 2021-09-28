<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\TagSlug;
use Str;
use ValidationException;

class PostTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.posttranslation.saving', function ($postTranslation, $user) {

        });

        // Before Save
        $event->listen('dynamedia.posts.posttranslation.saving', function ($postTranslation, $user) {

        });

        // After Save
        $event->listen('dynamedia.posts.posttranslation.saved', function ($postTranslation, $user) {

        });

        // Before Delete
        $event->listen('dynamedia.posts.posttranslation.deleting', function ($postTranslation, $user) {

        });

        // After Delete
        $event->listen('dynamedia.posts.posttranslation.deleted', function ($postTranslation, $user) {

        });
    }
}

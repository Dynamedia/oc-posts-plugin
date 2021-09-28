<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\TagSlug;
use Str;
use ValidationException;

class CategoryTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.categorytranslation.saving', function ($categoryTranslation, $user) {

        });

        // Before Save
        $event->listen('dynamedia.posts.categorytranslation.saving', function ($categoryTranslation, $user) {

        });

        // After Save
        $event->listen('dynamedia.posts.categorytranslation.saved', function ($categoryTranslation, $user) {

        });

        // Before Delete
        $event->listen('dynamedia.posts.categorytranslation.deleting', function ($categoryTranslation, $user) {

        });

        // After Delete
        $event->listen('dynamedia.posts.categorytranslation.deleted', function ($categoryTranslation, $user) {

        });
    }
}

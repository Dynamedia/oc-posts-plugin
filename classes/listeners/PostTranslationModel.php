<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\PostSlug;
use Str;
use ValidationException;
use Lang;

class PostTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.posttranslation.validating', function ($postTranslation, $user) {
            $postTranslation->slug = Str::slug($postTranslation->slug);

            if (!PostSlug::isAvailable($postTranslation->native->id, $postTranslation->slug)) {
                throw new ValidationException(
                    ['slug' => Lang::get('dynamedia.posts::lang.validation.slug_unavailable', ['slug' => $postTranslation->slug])]
                );
            }

            $postTranslation->prePopulateAttributes();
        });

        // Before Save
        $event->listen('dynamedia.posts.posttranslation.saving', function ($postTranslation, $user) {
            $postTranslation->body_text = $postTranslation->body->getTextContent();
        });

        // After Save
        $event->listen('dynamedia.posts.posttranslation.saved', function ($postTranslation, $user) {
            $slug = $postTranslation->native->postslugs()->firstOrCreate([
                'slug' => $postTranslation->slug,
            ]);
            $postTranslation->postslugs()->sync($slug->id, false);

            $postTranslation->native->invalidateBodyCache();
            $postTranslation->native->invalidateTranslatedAttributesCache();
            $postTranslation->native->invalidateSeoCache();
        });

        // Before Delete
        $event->listen('dynamedia.posts.posttranslation.deleting', function ($postTranslation, $user) {
            // Remove the pivot record but don't attempt to delete the slug record. It can still resolve to the post
            $postTranslation->postslugs()->detach();
        });

        // After Delete
        $event->listen('dynamedia.posts.posttranslation.deleted', function ($postTranslation, $user) {

        });
    }
}

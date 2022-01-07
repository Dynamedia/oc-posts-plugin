<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\TagSlug;
use Str;
use ValidationException;
use Lang;

class TagTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.tagtranslation.validating', function ($tagTranslation, $user) {
            $tagTranslation->slug = Str::slug($tagTranslation->slug);

            if (!TagSlug::isAvailable($tagTranslation->native->id, $tagTranslation->slug)) {
                throw new ValidationException(
                    ['slug' => Lang::get('dynamedia.posts::lang.validation.slug_unavailable', ['slug' => $tagTranslation->slug])]
                );
            }
            $tagTranslation->prePopulateAttributes();
        });

        // Before Save
        $event->listen('dynamedia.posts.tagtranslation.saving', function ($tagTranslation, $user) {
            $tagTranslation->body_text = $tagTranslation->body->getTextContent();
            $tagTranslation->slug = Str::slug($tagTranslation->slug);
        });

        // After Save
        $event->listen('dynamedia.posts.tagtranslation.saved', function ($tagTranslation, $user) {
            $slug = $tagTranslation->native->tagslugs()->firstOrCreate([
                'slug' => $tagTranslation->slug,
            ]);
            $tagTranslation->tagslugs()->sync($slug->id, false);

            $tagTranslation->native->invalidateBodyCache();
            $tagTranslation->native->invalidateTranslatedAttributesCache();
            $tagTranslation->native->invalidateSeoCache();
        });

        // Before Delete
        $event->listen('dynamedia.posts.tagtranslation.deleting', function ($tagTranslation, $user) {
            // Remove the pivot record but don't attempt to delete the slug record. It can still resolve to the tag
            $tagTranslation->tagslugs()->detach();
        });

        // After Delete
        $event->listen('dynamedia.posts.tagtranslation.deleted', function ($tagTranslation, $user) {

        });
    }
}

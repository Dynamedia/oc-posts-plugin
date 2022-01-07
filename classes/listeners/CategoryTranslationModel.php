<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\CategorySlug;
use Str;
use ValidationException;
use Lang;

class CategoryTranslationModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.categorytranslation.validating', function ($categoryTranslation, $user) {
            $categoryTranslation->slug = Str::slug($categoryTranslation->slug);

            if (!CategorySlug::isAvailable($categoryTranslation->native->id, $categoryTranslation->slug)) {
                throw new ValidationException(
                    ['slug' => Lang::get('dynamedia.posts::lang.validation.slug_unavailable', ['slug' => $categoryTranslation->slug])]
                );
            }
            $categoryTranslation->prePopulateAttributes();
        });

        // Before Save
        $event->listen('dynamedia.posts.categorytranslation.saving', function ($categoryTranslation, $user) {
            $categoryTranslation->body_text = $categoryTranslation->body->getTextContent();
            $categoryTranslation->slug = Str::slug($categoryTranslation->slug);
        });

        // After Save
        $event->listen('dynamedia.posts.categorytranslation.saved', function ($categoryTranslation, $user) {
            $slug = $categoryTranslation->native->categoryslugs()->firstOrCreate([
                'slug' => $categoryTranslation->slug,
            ]);
            $categoryTranslation->categoryslugs()->sync($slug->id, false);

            $categoryTranslation->native->invalidateBodyCache();
            $categoryTranslation->native->invalidateTranslatedAttributesCache();
            $categoryTranslation->native->invalidateSeoCache();
        });

        // Before Delete
        $event->listen('dynamedia.posts.categorytranslation.deleting', function ($categoryTranslation, $user) {
            // Remove the pivot record but don't attempt to delete the slug record. It can still resolve to the category
            $categoryTranslation->categoryslugs()->detach();
        });

        // After Delete
        $event->listen('dynamedia.posts.categorytranslation.deleted', function ($categoryTranslation, $user) {

        });
    }
}

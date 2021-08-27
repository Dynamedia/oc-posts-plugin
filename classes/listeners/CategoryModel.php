<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\CategorySlug;
use Str;
use ValidationException;

class CategoryModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.category.saving', function ($category, $user) {
            if (!CategorySlug::isAvailable($category->id, $category->slug)) {
                throw new ValidationException(['slug' => "Slug is not available"]);
            }
        });

        // Before Save
        $event->listen('dynamedia.posts.category.saving', function ($category, $user) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
            $category->slug = Str::slug($category->slug);

            $category->body_text = $category->body->getTextContent();
        });

        // After Save
        $event->listen('dynamedia.posts.category.saved', function ($category, $user) {
            // Create the categoryslug relationship. Required for auto redirection on change
            // Must be validated as unique per post/category (translations can share)
            $category->categoryslugs()->firstOrCreate([
                'slug' => $category->slug,
            ]);
        });

        // Before Delete
        $event->listen('dynamedia.posts.category.deleting', function ($category, $user) {
            $category->posts()->detach();
            $category->translations()->delete();
            $category->categoryslugs()->delete();
        });

        // After Delete
        $event->listen('dynamedia.posts.category.deleted', function ($category, $user) {

        });
    }
}

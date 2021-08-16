<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Models\PostSlug;
use Str;
use October\Rain\Argon\Argon;
use ValidationException;

class PostModel
{
    public function subscribe($event)
    {
        // Before Validate
        $event->listen('dynamedia.posts.post.saving', function ($post, $user) {
            if (!PostSlug::isAvailable($post->id, $post->slug)) {
                throw new ValidationException(['slug' => "Slug is not available"]);
            }
        });

        // Before Save
        $event->listen('dynamedia.posts.post.saving', function ($post, $user) {
            if (empty($post->author)) {
                if (!empty($user)) {
                    $post->author = $user;
                }
            }

            $post->slug = Str::slug($post->slug);

            if ($post->is_published && $post->published_at == null) {
                $post->published_at = Argon::now();
            }

            if (!$post->is_published) {
                $post->published_at = null;
            }
        });

        // After Save
        $event->listen('dynamedia.posts.post.saved', function ($post, $user) {
            if ($post->primary_category) {
                $post->categories()->sync([$post->primary_category->id], false);
            } else {
                if ($post->categories->count() > 0) {
                    $post->primary_category = $post->categories->first();
                }
            }
            // Create the postsslug relationship. Required for auto redirection on change
            // Must be validated as unique per post/category (translations can share)
            $post->postslugs()->firstOrCreate([
                'slug' => $post->slug,
            ]);
        });

        // Before Delete
        $event->listen('dynamedia.posts.post.deleting', function ($post, $user) {
        });

        // After Delete
        $event->listen('dynamedia.posts.post.deleted', function ($post, $user) {
            $this->categories()->detach();
            $this->tags()->detach();
            $this->translations()->delete();
            $this->postslugs()->delete();
        });
    }
}

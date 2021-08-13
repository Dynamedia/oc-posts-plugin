<?php namespace Dynamedia\Posts\Classes\Listeners;
use Str;
use October\Rain\Argon\Argon;

class PostModel
{
    public function subscribe($event)
    {
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
    }
}

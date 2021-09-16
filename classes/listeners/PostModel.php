<?php namespace Dynamedia\Posts\Classes\Listeners;
use Dynamedia\Posts\Controllers\Posts;
use Dynamedia\Posts\Controllers\PostTranslations;
use Dynamedia\Posts\Models\PostTranslation;
use Dynamedia\Posts\Models\Post;
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

            $post->body_text = $post->body->getTextContent();
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

            $post->invalidateTranslatedAttributesCache();

        });

        // Before Delete
        $event->listen('dynamedia.posts.post.deleting', function ($post, $user) {
            $post->postslugs()->delete();
            $post->categories()->detach();
            $post->tags()->detach();
            $post->translations()->delete();
        });

        // After Delete
        $event->listen('dynamedia.posts.post.deleted', function ($post, $user) {

        });

        // Move to a controller event

        Posts::extendFormFields(function($form, $model, $context)
        {
            if (!$model instanceof Post) {
                return;
            }

            if (str_contains($form->arrayName, "Post[body_document][template_body]")) {

                $option = null;

                if (!empty($model->body_document['template_body_options'])) {
                    $option = $model->body_document['template_body_options'];
                }

                $vars = post('Post');

                if (!empty($vars['body_document']['template_body_options'])) {
                    $option = $vars['body_document']['template_body_options'];
                }
                if ($option) {
                    $yaml = \Yaml::parse(\File::get($option));
                    if (!empty($yaml['fields'])) {
                        $form->addFields($yaml['fields']);
                    } elseif (!empty($yaml['tabs']['fields'])) {
                        $form->addTabFields($yaml['tabs']['fields']);
                    }
                }
            }
        });

        $event->listen('backend.form.extendFields', function ($widget) {

            // Only for the Page model
            if (!$widget->model instanceof PostTranslation) {
                return;
            }

            if (str_contains($widget->arrayName, "PostTranslation[body_document][template_body]")) {

                $option = null;

                if (!empty($model->body_document['template_body_options'])) {
                    $option = $model->body_document['template_body_options'];
                }

                $vars = post('PostTranslation');

                if (!empty($vars['body_document']['template_body_options'])) {
                    $option = $vars['body_document']['template_body_options'];
                }
                if ($option) {
                    $yaml = \Yaml::parse(\File::get($option));
                    if (!empty($yaml['fields'])) {
                        $widget->addFields($yaml['fields']);
                    } elseif (!empty($yaml['tabs']['fields'])) {
                        $widget->addTabFields($yaml['tabs']['fields']);
                    }
                }
            }
        });



    }
}

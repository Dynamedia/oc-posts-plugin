<?php namespace Dynamedia\Posts\Classes\Listeners;

use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Lang;

class StaticPagesMenu
{
    public function subscribe($event)
    {
        $event->listen('pages.menuitem.listTypes', function() {
            return [
                'posts-category'       => Lang::get('dynamedia.posts::lang.static_pages.menu_types.category'),
                'posts-all-categories' => Lang::get('dynamedia.posts::lang.static_pages.menu_types.all_categories'),
                'posts-tag'            => Lang::get('dynamedia.posts::lang.static_pages.menu_types.tag'),
                'posts-all-tags'       => Lang::get('dynamedia.posts::lang.static_pages.menu_types.all_tags'),
                'posts-post'           => Lang::get('dynamedia.posts::lang.static_pages.menu_types.post'),
                'posts-all-posts'      => Lang::get('dynamedia.posts::lang.static_pages.menu_types.all_posts'),
                'posts-category-posts' => Lang::get('dynamedia.posts::lang.static_pages.menu_types.category_posts'),
                'posts-tag-posts'      => Lang::get('dynamedia.posts::lang.static_pages.menu_types.tag_posts'),
            ];
        });

        $event->listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'posts-category' || $type == 'posts-all-categories') {
                return Category::getMenuTypeInfo($type);
            } elseif ($type == 'posts-tag' || $type == 'posts-all-tags') {
                return Tag::getMenuTypeInfo($type);
            }
            elseif ($type == 'posts-post' || $type == 'posts-all-posts' || $type == 'posts-category-posts' || $type == 'posts-tag-posts') {
                return Post::getMenuTypeInfo($type);
            }
        });

        $event->listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'posts-category' || $type == 'posts-all-categories') {
                return Category::resolveMenuItem($item, $url, $theme);
            }
            if ($type == 'posts-tag' || $type == 'posts-all-tags') {
                return Tag::resolveMenuItem($item, $url, $theme);
            }
            elseif ($type == 'posts-post' || $type == 'posts-all-posts' || $type == 'posts-category-posts' || $type == 'posts-tag-posts') {
                return Post::resolveMenuItem($item, $url, $theme);
            }
        });
    }

}

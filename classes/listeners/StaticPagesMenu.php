<?php namespace Dynamedia\Posts\Classes\Listeners;


use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;

class StaticPagesMenu
{
    public function subscribe($event)
    {
        $event->listen('pages.menuitem.listTypes', function() {
            return [
                'posts-category'       => 'Posts: A Category',
                'posts-all-categories' => 'Posts: All Categories',
                'posts-tag'            => 'Posts: A Tag',
                'posts-all-tags'       => 'Posts: All Tags',
                'posts-post'           => 'Posts: A Post',
                'posts-all-posts'      => 'Posts: All Posts',
                'posts-category-posts' => 'Posts: All Posts From Category',
                'posts-tag-posts'      => 'Posts: All Posts With Tag',
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

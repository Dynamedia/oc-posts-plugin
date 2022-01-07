<?php namespace Dynamedia\Posts\Classes\Acl;
use Backend\Models\User;

/**
 * Class AccessControl
 *
 * Static helpers for access control
 *
 * @package Dynamedia\Posts\Classes\Acl
 */

class AccessControl
{
    public static function getAvailablePermissions()
    {
        // Publishers and developers have full access. Some restricted non-system roles are created for more control
        return [
            'dynamedia.posts.access_plugin' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.access_plugin',
                'order' => 1000,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.create_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.create_posts',
                'order' => 1010,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.categorize_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.categorize_posts',
                'order' => 1020,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.tag_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.tag_posts',
                'order' => 1030,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.set_layout' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.set_post_layout',
                'order' => 1040,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_own_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.publish_own_posts',
                'order' => 1050,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_own_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.unpublish_own_posts',
                'order' => 1060,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_own_published_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.edit_own_published_posts',
                'order' => 1070,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_unpublished_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.delete_own_unpublished_posts',
                'order' => 1080,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_own_published_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.delete_own_published_posts',
                'order' => 1090,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.publish_all_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.publish_all_posts',
                'order' => 1100,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.unpublish_all_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.unpublish_all_posts',
                'order' => 1110,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_unpublished_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.edit_all_unpublished_posts',
                'order' => 1120,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.edit_all_published_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.edit_all_published_posts',
                'order' => 1130,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_unpublished_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.delete_all_unpublished_posts',
                'order' => 1140,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.delete_all_published_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.delete_all_published_posts',
                'order' => 1150,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.assign_posts' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.assign_posts',
                'order' => 1160,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_categories' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.view_categories',
                'order' => 1170,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_categories' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.manage_categories',
                'order' => 1180,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_tags' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.view_tags',
                'order' => 1190,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_tags' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.manage_tags',
                'order' => 1200,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_translations' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.manage_translations',
                'order' => 1200,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_slugs' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.manage_slugs',
                'order' => 1200,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.view_settings' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.view_settings',
                'order' => 1210,
                'roles' => ['publisher', 'developer']
            ],
            'dynamedia.posts.manage_settings' => [
                'tab' => 'dynamedia.posts::lang.acl.permissions_settings.tabs.posts',
                'label' => 'dynamedia.posts::lang.acl.permissions_settings.labels.manage_settings',
                'order' => 1220,
                'roles' => ['publisher', 'developer']
            ],
        ];
    }

    /**
     * Check if user has required permissions to view a post
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public static function postIsViewable($post, $user)
    {
        if ($post->is_published || $user) return true;
    }
    /**
     * Check if user has required permissions to edit
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public static function userCanEditPost($post, $user)
    {   // isDirty prevents failure if setting the attribute
        if ($post->is_published && !$post->isDirty('is_published')) {
            if (!$user->hasAccess('dynamedia.posts.edit_all_published_posts')
                && !($user->hasAccess('dynamedia.posts.edit_own_published_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        } else {
            if (!$user->hasAccess('dynamedia.posts.edit_all_unpublished_posts')
                && $user->id != $post->author_id) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Check if user has required permissions to delete
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public static function userCanDeletePost($post, $user)
    {
        if ($post->is_published) {
            if (!$user->hasAccess('dynamedia.posts.delete_all_published_posts')
                && !($user->hasAccess('dynamedia.posts.delete_own_published_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        } else {
            if (!$user->hasAccess('dynamedia.posts.delete_all_unpublished_posts')
                && !($user->hasAccess('dynamedia.posts.delete_own_unpublished_posts')
                    && $user->id == $post->author_id)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Check if user has required permissions to publish
     * @param Post $post
     * @param User $user
     * @return bool
     */
    public static function userCanPublishPost($post, $user)
    {
        if (!$user->hasAccess('dynamedia.posts.publish_all_posts')
            && !($user->hasAccess('dynamedia.posts.publish_own_posts')
                && $user->id == $post->author_id)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to unpublish
     * @param $user
     * @return bool
     */
    public static function userCanUnpublishPost($post, $user)
    {
        if (!$user->hasAccess('dynamedia.posts.unpublish_all_posts')
            && !($user->hasAccess('dynamedia.posts.unpublish_own_posts')
                && $user->id == $post->author_id)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to categorize posts
     * @param User $user
     * @return bool
     */
    public static function userCanCategorizePosts($user)
    {
        if (!$user->hasAccess('dynamedia.posts.categorize_posts')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to tag posts
     * @param User $user
     * @return bool
     */
    public static function userCanTagPosts($user)
    {
        if (!$user->hasAccess('dynamedia.posts.tag_posts')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to alter layouts
     * @param User $user
     * @return bool
     */
    public static function userCanSetLayout($user)
    {
        if (!$user->hasAccess('dynamedia.posts.set_layout')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to assign posts to other users
     * @param $user
     * @return bool
     */
    public static function userCanAssignPosts($user)
    {
        if (!$user->hasAccess('dynamedia.posts.assign_posts')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to view Categories
     * @param $user
     * @return bool
     */
    public static function userCanViewCategories($user)
    {
        if (!$user->hasAccess('dynamedia.posts.view_categories')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage Categories
     * @param $user
     * @return bool
     */
    public static function userCanManageCategories($user)
    {
        if (!$user->hasAccess('dynamedia.posts.manage_categories')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to view Tags
     * @param $user
     * @return bool
     */
    public static function userCanViewTags($user)
    {
        if (!$user->hasAccess('dynamedia.posts.view_tags')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage Tags
     * @param $user
     * @return bool
     */
    public static function userCanManageTags($user)
    {
        if (!$user->hasAccess('dynamedia.posts.manage_tags')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to view Settings
     * @param $user
     * @return bool
     */
    public static function userCanViewSettings($user)
    {
        if (!$user->hasAccess('dynamedia.posts.view_settings')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage Settings
     * @param $user
     * @return bool
     */
    public static function userCanManageSettings($user)
    {
        if (!$user->hasAccess('dynamedia.posts.manage_settings')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage Translations
     * @param $user
     * @return bool
     */
    public static function userCanManageTranslations($user)
    {
        if (!$user->hasAccess('dynamedia_posts.manage_translations')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if user has required permissions to manage Translations
     * @param $user
     * @return bool
     */
    public static function userCanManageSlugs($user)
    {
        if (!$user->hasAccess('dynamedia_posts.manage_slugs')) {
            return false;
        } else {
            return true;
        }
    }
}

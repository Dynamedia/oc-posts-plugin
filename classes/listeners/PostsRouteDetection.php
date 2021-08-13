<?php namespace Dynamedia\Posts\Classes\Listeners;

use App;
use Backend\Models\User;
use Event;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Models\Settings;

class PostsRouteDetection
{
    public function subscribe($event)
    {
        // Having completely semantic URLs is nice, but it causes post and category display routes to clash
        // We can deal with that by checking whether the provided slug is either a Post or a Category
        // It can't be both, as we don't allow it through validation. We will force render the relevant page.

        $event->listen('cms.page.beforeDisplay', function ($controller, $url, $page) {

            // Get info for potential clashing pages and the page the router actually matched
            $params = $controller->getRouter()->getParameters();
            $activeThemeCode = Theme::getActiveThemeCode();

            $postPage = [
                'page' => $pg = Settings::get('postPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $categoryPage = [
                'page' => $pg = Settings::get('categoryPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $tagPage = [
                'page' => $pg = Settings::get('tagPage'),
                'url'  => $pg ? Page::url($pg, $params) : null
            ];
            $matchedPage = [
                'page' => $pg = $page->getFileNameParts()[0],
                'url' => $pg ? Page::url($pg, $params) : null
            ];

            // Logic attempts to avoid querying both Posts and Categories if at all possible
            // But no way to avoid if the post and category pages do clash

            // Post Page
            if ($matchedPage['url'] == $postPage['url']) {
                Event::fire('dynamedia.posts.matchedPostRoute');
                $post = Post::getPost(['optionsSlug' => $this->extractSlug($controller)]);
            }

            // Category Page
            if ($matchedPage['url'] == $categoryPage['url']) {
                Event::fire('dynamedia.posts.matchedCategoryRoute');
                $category = Category::getCategory(['optionsSlug' => $this->extractSlug($controller)]);
            }

            // Tag Page
            if ($matchedPage['url'] == $tagPage['url']) {
                Event::fire('dynamedia.posts.matchedTagRoute');
                $tag = Tag::getTag($this->extractSlug($controller));
            }

            if (!empty($post)) {
                $newPage = Page::loadCached($activeThemeCode, $postPage['page']);
                $post->getCmsLayout() ? $newPage->layout = $post->getCmsLayout() : null;
                App::instance('dynamedia.posts.post', $post);
                return $newPage;
            }

            if (!empty($category)) {
                $newPage = Page::loadCached($activeThemeCode,$categoryPage['page']);
                $category->getCmsLayout() ? $newPage->layout = $category->getCmsLayout() : null;
                App::instance('dynamedia.posts.category', $category);
                return $newPage;
            }

            // Tags can't clash with Posts and Categories so can share the same name
            if (!empty($tag)) {
                $tag->getCmsLayout() ? $page->layout = $tag->getCmsLayout() : null;
                App::instance('dynamedia.posts.tag', $tag);
            }
        });
    }

    /**
     * Check for a relevant url parameter
     * @param $controller
     * @return string|null
     */
    private function extractSlug($controller)
    {
        $slug = null;

        if ($controller->param('postsPostSlug')) {
            $slug = $controller->param('postsPostSlug');
        } elseif ($controller->param('postsCategorySlug')) {
            $slug = $controller->param('postsCategorySlug');
        } elseif ($controller->param('postsFullPath')) {
            $slug = basename($controller->param('postsFullPath'));
        } elseif ($controller->param('postsCategoryPath')) {
            $slug = basename($controller->param('postsCategoryPath'));
        } elseif ($controller->param('postsTagSlug')) {
            $slug = $controller->param('postsTagSlug');
        }

        return $slug;
    }
}

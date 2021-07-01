<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Backend\Models\User as BackendUser;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Settings;
use October\Rain\Argon\Argon;
use Dynamedia\Posts\Traits\PaginationTrait;
use Cache;

class DisplayUser extends ComponentBase
{
    use PaginationTrait;

    public $user;
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'Display a User',
            'description' => 'A user and their posts'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->setUser();

        if (!$this->user) {
            return $this->controller->run('404');
        }

        $this->setPosts();

    }

    private function setUser()
    {
        $username = $this->param('postsUsername');

        if (!$username) return;

        $this->user = BackendUser::whereHas('profile', function ($q) use ($username) {
            $q->where('username', $username);
        })
            ->with('profile','avatar')
            ->first();
    }

    private function setPosts()
    {
        $postListOptions = [
            'optionsPage'        => $this->getRequestedPage(),
            'optionsSort'        => Settings::get('userPostsListSortOrder'),
            'optionsPerPage'     => Settings::get('userPostsListPerPage'),
            'optionsUsername'    => $this->param('postsUsername')
        ];

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }
}

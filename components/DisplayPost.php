<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use BackendAuth;
use App;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Acl\AccessControl;
use Dynamedia\Posts\Classes\Helpers\Form;
use Dynamedia\Posts\Classes\Seo\PostsObjectSeoParser;
use Dynamedia\Posts\Models\Settings;
use Dynamedia\Posts\Traits\PaginationTrait;
use Input;



class DisplayPost extends ComponentBase
{
    use PaginationTrait;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.display_post.name',
            'description' => 'dynamedia.posts::lang.components.display_post.description'
        ];
    }

    public $post;
    public $paginator;

    public function defineProperties()
    {
        return [

        ];
    }

    public function onRun()
    {
        $this->setPost();

        // 404 if post not found
        if (!$this->post) {
            return $this->controller->run('404');
        }
        // Check that we are at the right url. If not, redirect and get back here.
        if ($this->currentPageUrl() != $this->post->url) {
            return redirect($this->post->url, 301);
        }
        // Check publishing status and permissions
        if (!AccessControl::postIsViewable($this->post, BackendAuth::getUser())) {
            try {
                return $this->controller
                    ->setStatusCode(403)->run('403');
            } catch (\Exception $e) {
                return response("Not authorised", 403);
            }
        }

        $this->post->setSeo();

        $this->setPaginator();
    }


    public function getRequestedPage()
    {
        return (int) Input::get('page') > 0 ? (int) Input::get('page') : 1;
    }

    private function setPost()
    {
        if (App::bound('dynamedia.posts.post')) {
            $this->post = App::make('dynamedia.posts.post');
        }
    }

    private function setPaginator()
    {
        $body = $this->post->body;
        $pages = $body->getPages();
        $page = $body->renderPage($this->getRequestedPage());

        $paginatorOptions = [
            'items'         => $page,
            'totalResults'  => count($pages),
            'itemsPerPage'  => 1,
            'requestedPage' => $this->getRequestedPage()
        ];

        $this->paginator = $this->getPaginator($paginatorOptions, $this->currentPageUrl());
    }

}

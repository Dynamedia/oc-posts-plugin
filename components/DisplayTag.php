<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Helpers\Form;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Lang;
use App;
use Input;

class DisplayTag extends ComponentBase
{
    public $tag;
    public $posts;

    public function componentDetails()
    {
        return [
            'name'        => 'Display Tag',
            'description' => 'Display a tag with posts'
        ];
    }

    public function defineProperties()
    {
        return [
            // All component settings moved to backend settings area
        ];
    }

    public function onRun()
    {
        $this->setTag();
        //$this->tag = json_decode($this->tag->toJson());


        if (!$this->tag) return $this->controller->run('404');

        $this->setPosts();
    }

    private function setTag()
    {
        if (App::bound('dynamedia.posts.tag')) {
            $this->tag = App::make('dynamedia.posts.tag');
        }
    }

    public function setPosts()
    {
        $postListOptions = [
            'optionsTagId'      => $this->tag->id,
            'optionsSort'       => $this->tag->post_list_sort,
            'optionsPage'       => $this->getRequestedPage(),
            'optionsPerPage'    => $this->tag->post_list_per_page
        ];

        $this->posts = Post::getPostsList($postListOptions);

    }

    public function getRequestedPage()
    {
        return (int) Input::get('page') > 0 ? (int) Input::get('page') : 1;
    }

}

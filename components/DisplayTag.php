<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Dynamedia\Posts\Classes\Helpers\Form;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Tag;
use Lang;
use App;

class DisplayTag extends ComponentBase
{
    public $tag = null;

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
       $this->posts = $this->tag->getPosts();
    }

    public function getSortOrderOptions()
    {
        return Form::getComponentSortOptions();
    }
}

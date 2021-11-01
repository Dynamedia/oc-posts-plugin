<?php namespace Dynamedia\Posts\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Traits\PaginationTrait;
use App;

class DisplayCategory extends ComponentBase
{
    use PaginationTrait;

    public function componentDetails()
    {
        return [
            'name'        => 'dynamedia.posts::lang.components.display_category.name',
            'description' => 'dynamedia.posts::lang.components.display_category.description'
        ];
    }

    public $category = null;
    public $posts;

    public function defineProperties()
    {
        return [
            // All component settings moved into backend settings area.
        ];
    }

    public function onRun()
    {
        $this->setCategory();

        // Check that we are at the right url. If not, redirect and get back here.
            if (!$this->category) {
                return $this->controller->run('404');
            }

            if ($this->currentPageUrl() != $this->category->url) {
                return redirect($this->category->url, 301);
            }

        $this->category->setSeo();

        $this->setPosts();
    }

    private function setCategory()
    {
        if (App::bound('dynamedia.posts.category')) {
            $this->category = App::make('dynamedia.posts.category');
        }
    }

    public function setSchema()
    {
        $graph = App::make('dynamedia.posts.graph');

        $graph->getWebpage()
                ->setProperty("@id", $this->category->url . "#wepbage")
                ->url($this->category->url)
                ->title($this->category->name)
                ->description(strip_tags($this->category->excerpt));

        $graph->getBreadcrumbs()
            ->setProperty("@id", $this->category->url . "#breadcrumbs");

        foreach ($this->category->getCachedPathFromRoot() as $item) {
            $graph->addBreadcrumb($item['name'], $item['url']);
        }
    }

    public function setPosts()
    {
        $postListOptions = [
            'optionsCategoryIds' => $this->category->post_list_ids,
            'optionsSort'        => $this->category->post_list_sort,
            'optionsPage'        => $this->getRequestedPage(),
            'optionsPerPage'     => $this->category->post_list_per_page,
        ];

        $postList = Post::getPostsList($postListOptions);

        $this->posts = $this->getPaginator($postList, $this->currentPageUrl());
    }
}

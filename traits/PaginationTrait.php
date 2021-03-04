<?php namespace Dynamedia\Posts\Traits;
use Illuminate\Pagination\LengthAwarePaginator;
use Input;


Trait PaginationTrait {

    private function getPaginator($array, $url)
    {
        if (empty($array['items'])) return [];

        $paginator = new LengthAwarePaginator(
            $array['items'],
            $array['totalResults'],
            $array['itemsPerPage'],
            $array['requestedPage']
        );

        return $paginator->withPath($url);
    }

    public function getRequestedPage()
    {
        return (int) Input::get('page') > 0 ? (int) Input::get('page') : 1;
    }

}
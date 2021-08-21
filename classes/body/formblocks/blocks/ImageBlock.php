<?php namespace Dynamedia\Posts\Classes\Body\Formblocks\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class ImageBlock
{
    const view = 'dynamedia.posts::blocks.image';

    private $block;
    private $html;
    private $contents = [];


    public function __construct($block)
    {
        $this->block = $block;
        $this->parseBlock();
    }

    private function parseBlock()
    {
        $parsed = \View::make('dynamedia.posts::blocks.image', ['block' => $this->block])->render();
        //dd($parsed);
        try {

            //$this->html = $parsed;
        } catch (\Exception $e) {
            //
        }
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getContents()
    {
        return $this->contents;
    }
}

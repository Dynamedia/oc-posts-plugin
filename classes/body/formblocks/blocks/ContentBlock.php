<?php namespace Dynamedia\Posts\Classes\Body\Formblocks\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class ContentBlock
{
    const view = 'dynamedia.posts::blocks.content';

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
        //todo via the view
        try {
            $parsed = Content::load(Theme::getActiveTheme(), $this->block['block']['cms_content'])->parseMarkup();
            $this->html = $parsed;
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

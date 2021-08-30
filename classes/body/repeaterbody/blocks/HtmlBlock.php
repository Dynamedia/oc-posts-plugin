<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class HtmlBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.html_block';

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
        $html = !empty($this->block['block']['content']) ? $this->block['block']['content'] : [];
        try {
            $this->html = \View::make(self::view, [
                'block_id' => !empty($this->block['block']['block_id']) ? $this->block['block']['block_id'] : null,
                'html' => $html,
            ])->render();
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

    /**
     *
     * Create an array compatible with the output of a repeater to use when updating the form with dirty data
     *
     * @param string $content
     * @param array $image
     * @return array[]
     */
    public static function makePreviewBlock($content)
    {
        return [
            'block' => [
                'content' => $content,
            ],
            'group' => '_html_block'
        ];
    }
}

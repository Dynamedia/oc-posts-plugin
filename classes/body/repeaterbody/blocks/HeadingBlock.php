<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class HeadingBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.heading_block';

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
            $this->html = \View::make(self::view, [
                'block_id' => !empty($this->block['block']['block_id']) ? $this->block['block']['block_id'] : null,
                'type' => $this->block['block']['heading_type'],
                'content' => $this->block['block']['content'],
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
    public static function makePreviewBlock($type, $content)
    {
        return [
            'block' => [
                'heading_type' => $type,
                'content' => $content,
            ],
            '_group' => 'heading_block'
        ];
    }
}

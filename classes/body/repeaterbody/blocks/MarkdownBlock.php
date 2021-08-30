<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;


class MarkdownBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.markdown_block';

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
        // todo via the view
        $content = !empty($this->block['block']['content']) ? $this->block['block']['content'] : [];
        $image = !empty($this->block['block']['image']) ? $this->block['block']['image'] : [];

        $parsedImage = ImageBlock::parseImage($image);

        $this->html = \View::make(self::view, [
            'block_id' => !empty($this->block['block']['block_id']) ? $this->block['block']['block_id'] : null,
            'content' => $content,
            'image' => $parsedImage
            ])->render();
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
    public static function makePreviewBlock($content, $image = [])
    {
        return [
            'block' => [
                'content' => $content,
                'image' => $image
            ],
            'group' => '_markdown_block'
        ];
    }
}

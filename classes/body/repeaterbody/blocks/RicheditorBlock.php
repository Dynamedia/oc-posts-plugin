<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;


class RicheditorBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.richeditor_block';

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
            'content' => $content,
            'image' => $parsedImage,
            'image_style' => !empty($image['image_style']) ? $image['image_style'] : ''
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
            'group' => '_richeditor_block'
        ];
    }
}

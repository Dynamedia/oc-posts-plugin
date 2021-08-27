<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class ImageBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.image_block';

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
        $image = !empty($this->block['block']) ? $this->block['block'] : [];
        try {
            $parsed = self::parseImage($image);
            $this->html = $parsed;
        } catch (\Exception $e) {
            //
        }
    }

    public static function parseImage($image)
    {
        return \View::make(self::view, ['image' => $image])->render();
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
    public static function makePreviewBlock($default, $alt, $caption, $reponsive)
    {
        return [
            'block' => [
                'default' => $default,
                'alt'   => $alt,
                'caption' => $caption,
                'responsive' => $reponsive
            ],
            '_group' => 'image_block'
        ];
    }
}

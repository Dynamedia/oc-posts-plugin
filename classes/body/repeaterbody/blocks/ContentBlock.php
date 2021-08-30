<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;
use Cms\Classes\Content;
use Cms\Classes\Theme;

class ContentBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.cms_content_block';

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
            $content = Content::load(Theme::getActiveTheme(), $this->block['block']['cms_content_block'])->parseMarkup();
            $this->html = \View::make(self::view, [
                'block_id' => !empty($this->block['block']['block_id']) ? $this->block['block']['block_id'] : null,
                'content' => $content,
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
                'cms_content_block' => $content,
            ],
            '_group' => 'cms_content_block'
        ];
    }
}

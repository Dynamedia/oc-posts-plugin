<?php namespace Dynamedia\Posts\Classes\Body\Formblocks;


use Dynamedia\Posts\Classes\Body\Formblocks\Blocks\ContentBlock;
use Dynamedia\Posts\Classes\Body\Formblocks\Blocks\ImageBlock;
use Dynamedia\Posts\Classes\Body\Formblocks\Blocks\PartialBlock;
use Dynamedia\Posts\Classes\Body\Formblocks\Blocks\SectionBlock;

class FormBlockBody
{
    public $blocks;
    public $contents = [];
    public $html;

    private $blocktypes = [
        'section' => [
            'class' => SectionBlock::class,
        ],
        'cms_content' => [
            'class' => ContentBlock::class,
        ],
        'cms_partial' => [
            'class' => PartialBlock::class,
        ],
        'image' => [
            'class' => ImageBlock::class,
        ]
    ];

    public function __construct($body)
    {
        $this->blocks = $body;
        $this->processBlocks();
    }

    public function processBlocks()
    {
        foreach ($this->blocks as $block) {
            try {
                if (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
                    $blockObject = $this->getBlockClass($block);
                    $this->html = $this->html . $blockObject->getHtml();
                    $this->contents[] = $blockObject->getHtml();
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function render()
    {
        return $this->html;
    }


    private function getBlockClass($block)
    {
        if (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
            return new $this->blocktypes[$blockName]['class']($block);
        }
    }


}

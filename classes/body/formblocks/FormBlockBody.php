<?php namespace Dynamedia\Posts\Classes\Body\Formblocks;


use Dynamedia\Posts\Classes\Body\Formblocks\Blocks\SectionBlock;

class FormBlockBody
{
    public $blocks;
    public $contents = [];
    public $html;

    private $blocktypes = [
        'section' => [
            'class' => SectionBlock::class,
        ]
    ];

    public function __construct($body)
    {
        $this->blocks = $body;
    }

    public function render()
    {
        foreach ($this->blocks as $block) {
            if (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
                $blockObject = $this->getBlockClass($block);
                $this->html = $this->html . $blockObject->getHtml();
                $this->contents[] = $blockObject->getHtml();
            }
        }
    }


    private function getBlockClass($block)
    {
        if (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
            return new $this->blocktypes[$blockName]['class']($block);
        }
    }


}

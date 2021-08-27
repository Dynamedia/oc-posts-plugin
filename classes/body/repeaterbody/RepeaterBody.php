<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody;

use Dynamedia\Posts\Classes\Body\Body;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\ContentBlock;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\HtmlBlock;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\ImageBlock;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\PartialBlock;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\RicheditorBlock;
use Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks\MarkdownBlock;

class RepeaterBody extends Body
{
    private $blocks;

    private $blocktypes = [
        'richeditor_block' => [
            'class' => RicheditorBlock::class,
        ],
        'markdown_block' => [
            'class' => MarkdownBlock::class,
        ],
        'html_block' => [
            'class' => HtmlBlock::class,
        ],
        'cms_content_block' => [
            'class' => ContentBlock::class,
        ],
        'cms_partial_block' => [
            'class' => PartialBlock::class,
        ],
        'image_block' => [
            'class' => ImageBlock::class,
        ]
    ];

    public function __construct($body)
    {
        if (!empty($body['repeater_body'])) {
            $this->blocks = $body['repeater_body'];
            $this->processBlocks();
        }
    }

    /**
     * Iterate the blocks in the repeater, rendering them one by one.
     */
    private function processBlocks()
    {
        $html = '';

        foreach ($this->blocks as $block) {
            try {
                if ($block['_group'] == 'pagebreak') {
                    $this->pages[] = $html;
                    $html = '';
                }
                elseif (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
                    $blockObject = $this->getBlockClass($block);
                    $html .= $blockObject->getHtml();
                    //$this->contents[] = $blockObject->getHtml();
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        $this->pages[] = $html;
    }



    /**
     * Get an object relating to the block _group name
     *
     * @param $block
     * @return mixed Block object
     */
    private function getBlockClass($block)
    {
        if (array_key_exists($blockName = $block['_group'], $this->blocktypes)) {
            return new $this->blocktypes[$blockName]['class']($block);
        }
    }

    /**
     * Parse the repeater/nestedform field arrayName and return the block item
     *
     * @param $model
     * @param $field
     * @return false|mixed
     */
    public static function getBlockFromRepeater($model, $field)
    {
        try {
            parse_str($field->arrayName, $result);
            $keys = self::array_keys_multi($result);
            // Model -> body -> repeater_body -> INDEX -> block
            return $model->body_document['repeater_body'][$keys[3]];
        } catch (\Exception $e) {
            return false;
        }
    }

    static function array_keys_multi(array $array)
    {
        $keys = array();

        foreach ($array as $key => $value) {
            $keys[] = strtolower($key);

            if (is_array($value)) {
                $keys = array_merge($keys, self::array_keys_multi($value));
            }
        }

        return $keys;
    }


}

<?php namespace Dynamedia\Posts\Classes\Body\Repeaterbody\Blocks;
use Cms\Classes\Controller;
use Cms\Classes\Partial;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Twig\TwigFunctions;

class PartialBlock
{
    const view = 'dynamedia.posts::repeaterbody.blocks.cms_partial_block';

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
        //todo make sure this works in the backend
        try {
            $partial = Partial::load(Theme::getActiveTheme(), $this->block['block']['cms_partial']);

            $controller = Controller::getController();
            if (!$controller) {
                $controller = new Controller();
            }

            $parsed = $controller->renderPartial($partial->fileName, $this->getDataArray());

            $this->html = \View::make(self::view, [
                'block_id' => !empty($this->block['block']['block_id']) ? $this->block['block']['block_id'] : null,
                'content' => $parsed,
            ])->render();
        } catch (\Exception $e) {
            //
        }
    }

    private function getDataArray()
    {
        if (!empty($this->block['block']['data'])) {
            return [
                // todo This is not sensible. Make the extract method and let the twig function call it
                'data' => TwigFunctions::extractRepeaterData($this->block['block']['data'])
            ];
        } else return [];
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
    public static function makePreviewBlock($content, $data = [])
    {
        return [
            'block' => [
                'cms_partial' => $content,
                'data' => $data
            ],
            '_group' => 'cms_partial_block'
        ];
    }
}

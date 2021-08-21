<?php namespace Dynamedia\Posts\Classes\Body\Formblocks\Blocks;
use Cms\Classes\Controller;
use Cms\Classes\Partial;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Twig\TwigFunctions;

class PartialBlock
{
    const view = 'dynamedia.posts::blocks.partial';

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
        //todo make sure this works in the backend
        try {
            $partial = Partial::load(Theme::getActiveTheme(), $this->block['block']['cms_partial']);

            $controller = Controller::getController();

            $parsed = $controller->renderPartial($partial->fileName, $this->getDataArray());
            $this->html = $parsed;
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
}

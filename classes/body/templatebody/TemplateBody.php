<?php namespace Dynamedia\Posts\Classes\Body\Templatebody;

use Cms\Classes\Controller;
use Cms\Classes\Partial;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Body\Body;
use File;
use Yaml;

class TemplateBody extends Body
{

    // todo IMPORTANT add caching here

    public function __construct($bodyDocument)
    {
        $this->pages = [];
        try {
            $yaml = Yaml::parse(File::get($bodyDocument['template_body_options']));
            $partialPath = "body_templates/" . $yaml['partial'];
            $partial = Partial::load(Theme::getActiveTheme(), $partialPath);

            $controller = Controller::getController();
            if (!$controller) {
                $controller = new Controller();
            }

        } catch (\Exception $e) {
            return;
        }

        $this->pages = explode('<hr class="pagebreak">', $controller->renderPartial($partial->fileName, $bodyDocument['template_body']));
    }

}

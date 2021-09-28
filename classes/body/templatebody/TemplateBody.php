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

    public function __construct($model)
    {
        parent::__construct($model);
    }

    protected function setPages()
    {
        $this->pages = [];

        try {
            $yaml = Yaml::parse(File::get($this->model->body_document['template_body_options']));
            $partialPath = "body_templates/" . $yaml['partial'];
            $partial = Partial::load(Theme::getActiveTheme(), $partialPath);

            $controller = Controller::getController();
            if (!$controller) {
                $controller = new Controller();
            }

        } catch (\Exception $e) {
            return;
        }

        $this->pages = explode('<hr class="pagebreak">', $controller->renderPartial($partial->fileName, [
            'template' => $this->model->body_document['template_body'],
            'post' => $this->model
            ]));
    }


    public static function getConfigCacheKey($filePath)
    {
        return md5("dynamedia_posts_template_" . $filePath . "_" . \Lang::getLocale());
    }

    public static function parseConfig($filePath)
    {
        return Yaml::parse(File::get($filePath));
    }

}

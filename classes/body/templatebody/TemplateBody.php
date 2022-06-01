<?php namespace Dynamedia\Posts\Classes\Body\Templatebody;

use Cms\Classes\Controller;
use Cms\Classes\Partial;
use Cms\Classes\Theme;
use Dynamedia\Posts\Classes\Body\Body;
use File;
use Yaml;

class TemplateBody extends Body
{

    public function __construct($model)
    {
        parent::__construct($model);
    }

    protected function setPages()
    {
        $this->pages = [];

        try {
            $yaml = Yaml::parse(self::getYamlFile($this->model->body_document['template_body_options']));
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

    /**
     * Attempt to load the yaml file from the theme. On failure, try the parent theme
     * @throws \ApplicationException
     */
    public static function getYamlFile($relativePath)
    {
        // todo refactor for robustness and store backup to db
        try {
            $yaml = File::get(Theme::getActiveTheme()->getPath() . $relativePath);
        } catch (\Exception $e) {
            $yaml = File::get(Theme::getActiveTheme()->getParentTheme()->getPath() . $relativePath);
        }
        return $yaml;
    }

    /**
     * Return the full path
     */
    public static function getYamlFilePath($relativePath)
    {
        if (File::exists(Theme::getActiveTheme()->getPath() . $relativePath)) {
            return Theme::getActiveTheme()->getPath() . $relativePath;
        } elseif (File::exists(Theme::getActiveTheme()->getParentTheme()->getPath() . $relativePath)) {
            return Theme::getActiveTheme()->getParentTheme()->getPath() . $relativePath;
        }
        return false;
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

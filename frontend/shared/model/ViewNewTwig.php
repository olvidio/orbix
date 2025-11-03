<?php

namespace frontend\shared\model;

use core\ServerConf;
use Exception;
use jblond\TwigTrans\Translation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require_once(\core\ConfigGlobal::$dir_libs . '/vendor/autoload.php');

/**
 * ViewNewTwig: motor de plantillas Twig para el árbol de frontend/
 *
 * - Mantiene la misma API pública que core\ViewTwig (renderizar)
 * - Resuelve rutas de plantillas bajo frontend/... sustituyendo controller|model por view
 */
class ViewNewTwig extends Environment
{
    /** @var FilesystemLoader */
    private $loader;

    public function __construct(string $dirname, array $paths = [])
    {
        $abs_dir = $this->setAbsolutePath($dirname);
        $loader = new FilesystemLoader($abs_dir);

        foreach ($paths as $namespace => $path) {
            $abs_path = $this->setAbsolutePath($path);
            $loader->addPath($abs_path, $namespace);
        }

        // añadir scripts globales
        $dir_js = $this->getJsPath();
        $loader->addPath($dir_js, 'global_js');

        $options = [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true,
        ];

        $filter = new TwigFilter('trans', function (Environment $env, $context, $string) {
            return Translation::TransGetText($string, []);
        }, ['needs_context' => true, 'needs_environment' => true]);

        parent::__construct($loader, $options);
        parent::addFilter($filter);
        parent::addExtension(new Translation());
    }

    private function setAbsolutePath(string $dirname): string
    {
        $base_dir = ServerConf::DIR . DIRECTORY_SEPARATOR . 'frontend';

        // reemplazo controller o model por view
        $patterns = ['/controller/', '/model/'];
        $replacements = ['view', 'view'];

        $new_dir = preg_replace($patterns, $replacements, $dirname);
        $new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);

        return $base_dir . DIRECTORY_SEPARATOR . $new_dir;
    }

    private function getJsPath(): string
    {
        // scripts están en la raíz del proyecto
        return ServerConf::DIR . DIRECTORY_SEPARATOR . 'scripts';
    }

    public function renderizar($name, $context): void
    {
        try {
            $tpl = $this->load($name);
        } catch (Exception $exception) {
            echo $exception->getMessage();
            die();
        }

        echo $tpl->render($context);
    }
}

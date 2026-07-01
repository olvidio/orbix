<?php

namespace frontend\shared\model;

use frontend\shared\config\OrbixRuntime;
use Exception;
use jblond\TwigTrans\Translation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

require_once(\src\shared\config\ConfigGlobal::$dir_libs . '/vendor/autoload.php');

/**
 * ViewNewTwig: motor de plantillas Twig para el árbol de frontend/
 *
 * - Mantiene la misma API pública que core.ViewTwig (renderizar)
 * - Resuelve rutas de plantillas bajo frontend/... sustituyendo controller|model por view
 */
class ViewNewTwig extends Environment
{
    /**
     * @param array<string, string> $paths
     */
    public function __construct(string $dirname, array $paths = [])
    {
        $abs_dir = $this->setAbsolutePath($dirname);
        $loader = new FilesystemLoader($abs_dir);

        foreach ($paths as $namespace => $path) {
            $abs_path = $this->setAbsolutePath($path);
            $loader->addPath($abs_path, $namespace);
        }

        $dir_js = $this->getJsPath();
        $loader->addPath($dir_js, 'global_js');

        $dir_shared_view = OrbixRuntime::dir() . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'shared' . DIRECTORY_SEPARATOR . 'view';
        $loader->addPath($dir_shared_view, 'shared');

        $options = [
            'cache' => false,
            'debug' => OrbixRuntime::isDebug(),
            'auto_reload' => OrbixRuntime::isDebug(),
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
        if ($dirname !== '' && $dirname[0] === DIRECTORY_SEPARATOR) {
            return $dirname;
        }

        $patterns = ['/controller/', '/model/'];
        $replacements = ['view', 'view'];
        $new_dir = preg_replace($patterns, $replacements, $dirname);
        $new_dir = is_string($new_dir) ? $new_dir : $dirname;
        $new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);

        return OrbixRuntime::dir() . DIRECTORY_SEPARATOR . $new_dir;
    }

    private function getJsPath(): string
    {
        return OrbixRuntime::dir() . DIRECTORY_SEPARATOR . 'scripts';
    }

    /**
     * @param array<string, mixed> $context
     */
    public function renderizar(string $name, array $context): void
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

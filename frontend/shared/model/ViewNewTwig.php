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
        // Rutas absolutas se devuelven tal cual.
        if ($dirname !== '' && $dirname[0] === DIRECTORY_SEPARATOR) {
            return $dirname;
        }

        // reemplazo controller o model por view
        $patterns = ['/controller/', '/model/'];
        $replacements = ['view', 'view'];
        $new_dir = preg_replace($patterns, $replacements, $dirname);
        $new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);

        // Namespaces que siguen en el arbol legacy /apps se resuelven desde la
        // raiz del proyecto (permite compartir plantillas con ViewTwig durante
        // la migracion).
        if (str_starts_with($new_dir, 'apps' . DIRECTORY_SEPARATOR)) {
            return OrbixRuntime::dir() . DIRECTORY_SEPARATOR . $new_dir;
        }

        $base_dir = OrbixRuntime::dir() . DIRECTORY_SEPARATOR . 'frontend';
        return $base_dir . DIRECTORY_SEPARATOR . $new_dir;
    }

    private function getJsPath(): string
    {
        // scripts están en la raíz del proyecto
        return OrbixRuntime::dir() . DIRECTORY_SEPARATOR . 'scripts';
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

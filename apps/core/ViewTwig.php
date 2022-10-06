<?php

namespace core;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Parent_;

require_once(ConfigGlobal::$dir_libs . '/vendor/autoload.php');

/**
 *
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 22/9/2010
 */
class ViewTwig extends \Twig_Environment
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Namespace
     *
     * @var \Twig_Loader_Filesystem
     */
    private $loader;


    /* CONSTRUCTOR -------------------------------------------------------------- */
    /**
     * Constructor de la classe.
     *
     * param string  $dirname Es el directorio donde est´an las plantillas de twig
     * param array $paths $namespace => $path los possibles directorios donde buscar plantillas, son el namespace. (se antepone @).
     *
     * return \Twig_Environment
     */
    function __construct($dirname, array $paths = [])
    {

        $abs_dir = $this->setAbsolutePath($dirname);

        $loader = new \Twig_Loader_Filesystem($abs_dir);

        foreach ($paths as $namespace => $path) {
            $abs_dir = $this->setAbsolutePath($path);
            $loader->addPath($abs_dir, $namespace);
        }

        $options = [
            'cache' => '/path/to/compilation_cache',
        ];
        $options = [];

        parent::__construct($loader, $options);
        parent::addExtension(new \Twig_Extensions_Extension_I18n());
    }

    private function setAbsolutePath($dirname)
    {
        $dir_apps = ConfigGlobal::$web_path . '/apps';
        $base_dir = $_SERVER['DOCUMENT_ROOT'] . $dir_apps;

        // reemplazo controller o model por view
        $patterns = array();
        $patterns[0] = '/controller/';
        $patterns[1] = '/model/';
        $replacements = array();
        $replacements[0] = 'view';
        $replacements[1] = 'view';

        $new_dir = preg_replace($patterns, $replacements, $dirname);
        $new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);
        $dir_templates = $base_dir . DIRECTORY_SEPARATOR . $new_dir;

        return $dir_templates;
    }
    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

}
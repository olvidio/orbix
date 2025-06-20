<?php

namespace frontend\shared\model;


use core\ConfigGlobal;

/**
 * Set
 *
 * Classe per a gestionar una col·lecció d'objectes.
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 22/9/2010
 */
class ViewNewPhtml
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Namespace
     *
     * @var string
     */
    private $snamespace;


    /* CONSTRUCTOR -------------------------------------------------------------- */
    /**
     * Constructor de la classe.
     *
     *
     */
    function __construct($namespace)
    {
        $this->snamespace = $namespace;
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    function renderizar($file, $variables = array())
    {

        extract($variables);

        ob_start();
        $dir_apps = ConfigGlobal::$web_path;
        $base_dir = $_SERVER['DOCUMENT_ROOT'] . $dir_apps;

        // reemplazo controller o model por view
        $patterns = [];
        $patterns[0] = '/controller/';
        $patterns[1] = '/model/';
        $replacements = [];
        $replacements[0] = 'view';
        $replacements[1] = 'view';
        $new_dir = preg_replace($patterns, $replacements, $this->snamespace);

        $new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);

        $fileName = $base_dir . DIRECTORY_SEPARATOR . $new_dir . DIRECTORY_SEPARATOR . $file;

        require $fileName;

        $out2 = ob_get_contents();

        ob_end_clean();

        echo $out2;
    }
}
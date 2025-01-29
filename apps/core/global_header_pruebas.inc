<?php
require __DIR__ . '/../../libs/vendor/autoload.php';
/*
namespace pruebas\apps\core {
    spl_autoload_extensions('.php');
    spl_autoload_register(__NAMESPACE__ . '\autoload');
    function autoload($className)
    {
        $dir_apps = '/pruebas/apps';
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $base_dir = $_SERVER['DOCUMENT_ROOT'] . $dir_apps;
        $fileName = $base_dir . DIRECTORY_SEPARATOR . $fileName;
        //echo "inc: ". $fileName."<br>";

        //Aclararse que pasa con apc, include/require vs spl_autoload.
        if (file_exists($fileName) && include($fileName)) {
            return TRUE;
        } else {
            // si hago esto no veo de donde viene el error!!
            //trigger_error("The class '$className' or the file '$fileName' failed to spl_autoload  ", E_USER_WARNING);
            return FALSE;
        }
    }
}

namespace pruebas\apps {

    //permisos/login_inc
    use permisos\model\MyCrypt;
    use core\ClasePropiedades;
    use DatosCampo;
    use core\Condicion;
    use core\Set;

    use core\ConfigGlobal;
    use core\GestorErrores;
}
*/

<?php

namespace orbix\apps\core {
    spl_autoload_extensions('.php');
    spl_autoload_register(__NAMESPACE__ . '\autoload');
    function autoload($className)
    {
        $dir_apps = '/orbix/apps';
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

namespace orbix\apps {
    //permisos/login_inc
    use permisos\model\MyCrypt;

    //use permisos\model\Perm;

    use core\ClasePropiedades;
    use core\DatosCampo;
    use core\Condicion;
    use core\Set;

    use core\ConfigGlobal;
    use core\GestorErrores;
}
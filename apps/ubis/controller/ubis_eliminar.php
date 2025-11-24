<?php

use core\ConfigGlobal;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = ConfigGlobal::MiUsuario();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');


$repo = 'src\\ubis\\application\\repositories\\' . $Qobj_pau .'Repository';
$Repository = new $repo($Qid_ubi);
if ($Repository->Eliminar($Qid_ubi) === false) {
    echo _("hay un error, no se ha eliminado");
    echo "\n" ;
}
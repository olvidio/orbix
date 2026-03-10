<?php

use core\ConfigGlobal;
use src\shared\infrastructure\ProvidesRepositories;

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

// Clase auxiliar para usar el trait en contexto procedural
$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object {
        return $this->getRepository($entityType);
    }
};

function getRepository(string $obj_pau): object
{
    global $repositoryProvider;
    return $repositoryProvider->get($obj_pau);
}

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');


$Repository = getRepository($Qobj_pau);
if ($Repository->Eliminar($Qid_ubi) === false) {
    echo _("hay un error, no se ha eliminado");
    echo "\n" ;
}

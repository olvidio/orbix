<?php

use src\usuarios\application\repositories\GrupoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************


$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_usuario = (integer)strtok($a_sel[0], "#");
}
$Gruporepository = new GrupoRepository();
$oGrupo = $Gruporepository->findById($id_usuario);
if ($Gruporepository->Eliminar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $Gruporepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
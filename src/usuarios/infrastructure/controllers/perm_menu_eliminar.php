<?php

use src\usuarios\application\repositories\PermMenuRepository;
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
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
}

$PermMenuRepository = new PermMenuRepository();
$oUsuarioPerm = $PermMenuRepository->findById($Qid_item);
if ($PermMenuRepository->Eliminar($oUsuarioPerm) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $PermMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
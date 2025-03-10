<?php

use usuarios\model\entity\Role;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qrole = (string)filter_input(INPUT_POST, 'role');
$Qid_role = (integer)filter_input(INPUT_POST, 'id_role');
$Qsf = (integer)filter_input(INPUT_POST, 'sf');
$Qsv = (integer)filter_input(INPUT_POST, 'sv');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qdmz = (integer)filter_input(INPUT_POST, 'dmz');


$error_txt = '';

if ($Qrole) {
    if (!empty($Qid_role)) {
        $oRole = new Role(array('id_role' => $Qid_role));
    } else {
        $oRole = new Role();
    }
    $oRole->setRole($Qrole);
    $sf = !empty($Qsf) ? '1' : 0;
    $oRole->setSf($sf);
    $sv = !empty($Qsv) ? '1' : 0;
    $oRole->setSv($sv);
    $oRole->setPau($Qpau);
    $dmz = !empty($Qdmz) ? '1' : 0;
    $oRole->setDmz($dmz);
    if ($oRole->DBGuardar() === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $oRole->getErrorTxt();
    }
} else {
    $error_txt = _("debe poner un nombre");
}

ContestarJson::enviar($error_txt, 'ok');
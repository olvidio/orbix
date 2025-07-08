<?php

use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use src\usuarios\domain\value_objects\RoleName;
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
    $RoleRepository = new RoleRepository();
    if (!empty($Qid_role)) {
        $oRole = $RoleRepository->findById($Qid_role);
    } else {
        $id_role_new = $RoleRepository->getNewId();
        $oRole = new Role();
        $oRole->setId_role($id_role_new);
    }
    $oRole->setRole(new RoleName($Qrole));
    $sf = !empty($Qsf) ? '1' : 0;
    $oRole->setSf($sf);
    $sv = !empty($Qsv) ? '1' : 0;
    $oRole->setSv($sv);
    $oRole->setPau(new PauType($Qpau));
    $dmz = !empty($Qdmz) ? '1' : 0;
    $oRole->setDmz($dmz);
    if ($RoleRepository->Guardar($oRole) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $RoleRepository->getErrorTxt();
    }
} else {
    $error_txt = _("debe poner un nombre");
}

ContestarJson::enviar($error_txt, 'ok');
<?php

use inventario\domain\repositories\EquipajeRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// eliminar el equipaje y sus docs
// los grupos en egm deberían eliminarse por la base de datos, al tener una foreign key.
// los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.

$EquipajesRepository = new EquipajeRepository();
$oEquipaje = $EquipajesRepository->findById($Qid_equipaje);
if ($EquipajesRepository->Eliminar($oEquipaje) === FALSE) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $EquipajesRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');


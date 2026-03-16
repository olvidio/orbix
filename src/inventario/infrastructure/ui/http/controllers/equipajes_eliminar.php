<?php

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use web\ContestarJson;

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// eliminar el equipaje y sus docs
// los grupos en egm deberían eliminarse por la base de datos, al tener una foreign key.
// los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.

$EquipajesRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajesRepository->findById($Qid_equipaje);
if ($EquipajesRepository->Eliminar($oEquipaje) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $EquipajesRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');


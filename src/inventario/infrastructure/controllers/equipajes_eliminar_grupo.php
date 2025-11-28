<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use web\ContestarJson;

$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// Nuevo egm:
$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
$aWhere = [
    'id_equipaje' => $Qid_equipaje,
    'id_grupo' => $Qid_grupo,
];
$cEgm = $EgmRepository->getEgmes($aWhere);
if (!empty($cEgm)) {
    $oEgm = $cEgm[0];
    if ($EgmRepository->Eliminar($oEgm) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $EgmRepository->getErrorTxt();
    }
    // los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.
}

ContestarJson::enviar($error_txt, 'ok');


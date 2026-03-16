<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use web\ContestarJson;

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_item_egm = (integer)filter_input(INPUT_POST, 'id_item_egm');

$error_txt = '';

$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
if (!empty($Qid_item_egm)) {
    $oEgm = $EgmRepository->findById($Qid_item_egm);
} else {
    $aWhere = ['id_equipaje' => $Qid_equipaje, 'id_grupo' => $Qid_grupo];
    $cEgm = $EgmRepository->getEgmes($aWhere);
    $oEgm = $cEgm[0];
}

$texto = $oEgm->getTextoVo()->value();

$data = [
    'texto' => $texto,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);

<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_equipaje = FuncTablasSupport::inputInt($_POST, 'id_equipaje');
$Qid_grupo = FuncTablasSupport::inputInt($_POST, 'id_grupo');
$Qid_item_egm = FuncTablasSupport::inputInt($_POST, 'id_item_egm');

$error_txt = '';

/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
if (!empty($Qid_item_egm)) {
    $oEgm = $EgmRepository->findById($Qid_item_egm);
    if ($oEgm === null) {
        ContestarJson::enviar($error_txt, []);
        return;
    }
} else {
    $aWhere = ['id_equipaje' => $Qid_equipaje, 'id_grupo' => $Qid_grupo];
    $cEgm = $EgmRepository->getEgmes($aWhere);
    $oEgm = $cEgm[0];
}

$texto = $oEgm->getTextoVo()?->value() ?? '';

$data = [
    'texto' => $texto,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);

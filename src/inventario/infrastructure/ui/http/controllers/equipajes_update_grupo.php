<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Whereis;
use web\ContestarJson;

$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// Nuevo egm:
$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
$aWhere = [
    'id_equipaje' => $Qid_equipaje,
    'id_grupo' => $Qid_grupo,
    'id_lugar' => $Qid_lugar,
];
$cEgm = $EgmRepository->getEgmes($aWhere);
if (empty($cEgm)) {
    // nuevo
    $id_item_egm = $EgmRepository->getNewId();
    $oEgm = new Egm();
    $oEgm->setId_item($id_item_egm);
    $oEgm->setId_equipaje($Qid_equipaje);
    $oEgm->setId_grupo($Qid_grupo);
    $oEgm->setId_lugar($Qid_lugar);
    if ($EgmRepository->Guardar($oEgm) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $EgmRepository->getErrorTxt();
    }
} else {
    $oEgm = $cEgm[0];
    $id_item_egm = $oEgm->getId_item();
}

$WhereisRepository = $GLOBALS['container']->get(WhereisRepositoryInterface::class);
foreach ($a_sel as $id_doc) {
    $new_id = $WhereisRepository->getNewId();
    $oWhereis = new Whereis();
    $oWhereis->setId_item_whereis($new_id);
    $oWhereis->setId_item_egm($id_item_egm);
    $oWhereis->setId_doc($id_doc);
    if ($WhereisRepository->Guardar($oWhereis) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $WhereisRepository->getErrorTxt();
    }
}

$data['id_item_egm'] = $id_item_egm;

ContestarJson::enviar($error_txt, $data);


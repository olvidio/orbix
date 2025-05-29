<?php

use src\inventario\application\repositories\EgmRepository;
use src\inventario\application\repositories\WhereisRepository;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Whereis;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// Nuevo egm:
$EgmRepository = new EgmRepository();
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
    if ($EgmRepository->Guardar($oEgm) === FALSE) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $EgmRepository->getErrorTxt();
    }
} else {
    $oEgm = $cEgm[0];
    $id_item_egm = $oEgm->getId_item();
}

$WhereisRepository = new WhereisRepository();
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


<?php

use src\inventario\application\repositories\EgmRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_item_egm = (integer)filter_input(INPUT_POST, 'id_item_egm');

$error_txt = '';

$EgmRepository = new EgmRepository();
if (!empty($Qid_item_egm)) {
    $oEgm = $EgmRepository->findById($Qid_item_egm);
} else {
    $aWhere = ['id_equipaje' => $Qid_equipaje, 'id_grupo' => $Qid_grupo];
    $cEgm = $EgmRepository->getEgmes($aWhere);
    $oEgm = $cEgm[0];
}

$texto = $oEgm->getTexto();

$data = [
    'texto' => $texto,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);

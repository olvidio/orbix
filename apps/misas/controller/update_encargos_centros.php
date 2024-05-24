<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\entity\EncargoCtr;
use misas\domain\EncargoCtrId;
use misas\domain\repositories\EncargoCtrRepository;
use ubis\model\entity\Ubi;
use Ramsey\Uuid\Uuid as RamseyUuid;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_item = (string)filter_input(INPUT_POST, 'id_item');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qid_ctr = (integer)filter_input(INPUT_POST, 'id_ctr');

echo 'id_item: '.$Qid_item.'<br>';

$error_txt = '';

if ($Qque === 'nuevo') {
//    echo $Qid_enc.'-->'.$Qid_ctr.'<br>';
    $Uuid = new EncargoCtrId(RamseyUuid::uuid4()->toString());
    $EncargoCtrRepository = new EncargoCtrRepository();
    $EncargoCtr = new EncargoCtr();
    $EncargoCtr->setUuid_item($Uuid);
    $EncargoCtr->setId_ubi($Qid_ctr);
    $EncargoCtr->setId_enc($Qid_enc);
    if ($EncargoCtrRepository->Guardar($EncargoCtr) === FALSE) {
        $error_txt .= $EncargoCtrRepository->getErrorTxt();
    }  
}

if ($Qque === 'modificar') {
    $Qid_enc = (string)filter_input(INPUT_POST, 'id_enc');
    $Qid_ctr = (string)filter_input(INPUT_POST, 'id_ctr');




    $EncargoCtr = new EncargoCtr();
    $EncargoCtr = $EncargoCtr->findById($Qid_enc);

    $EncargoCtr = new EncargoCtr($Qid_enc);
    $EncargoCtr->setId_ubi($Qid_ctr);

    $jsondata['que'] = $Qque;
    if ($EncargoCtr->Guardar($EncargoCtr) === FALSE) {
        $error_txt .= $EncargoCtr->getErrorTxt();
    }
}

if ($Qque === 'borrar') {
    $Qid_enc = (string)filter_input(INPUT_POST, 'id_enc');
    $EncargoZona = new Encargo($Qid_enc);
    if ($EncargoZona->DBEliminar() === FALSE) {
        $error_txt .= $EncargoZona->getErrorTxt();
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();

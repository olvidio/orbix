<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\entity\EncargoCtr;
use misas\domain\repositories\EncargoCtrRepository;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

$error_txt = '';
if (($Qque === 'modificar') || ($Qque === 'nuevo')) {
    $Qid_enc = (string)filter_input(INPUT_POST, 'id_enc');
    $Qid_ctr = (string)filter_input(INPUT_POST, 'id_ctr');

    $EncargoCtrRepository = new EncargoCtrRepository();
    $EncargoCtr = $EncargoCtrRepository->findById($Qid_enc);
    if (is_null($InicialesSacd)) {
        $InicialesSacd = new InicialesSacd();
        $InicialesSacd->setId_nom($Qid_sacd);
    }

    $InicialesSacd->setIniciales($Qiniciales);
    $InicialesSacd->setColor($Qcolor);

    $EncargoCtr = new EncargoCtr($Qid_enc);
    $EncargoCtr->setId_ubi($Qid_ctr);

    $jsondata['que'] = $Qque;
    if ($EncargoCtrRepository->Guardar($EncargoCtr) === FALSE) {
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

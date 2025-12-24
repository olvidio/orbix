<?php

// INICIO Cabecera global de URL de controlador *********************************

use Illuminate\Http\JsonResponse;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
$Qt_start = (string)filter_input(INPUT_POST, 't_start');
$Qt_end = (string)filter_input(INPUT_POST, 't_end');

$error_txt = '';
if (empty($Qid_item_h)) {
    $error_txt .= _("Error: falta el id_item");
} else {

    $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
    $oEncargoHorario = $EncargoHorarioRepository->findById($Qid_item_h);

    if (!empty($Qt_start)) {
        $oEncargoHorario->setH_ini($Qt_start);
    }
    if (!empty($Qt_end)) {
        $oEncargoHorario->setH_fin($Qt_end);
    }

    if ($EncargoHorarioRepository->Guardar($oEncargoHorario) === FALSE) {
        $error_txt .= $EncargoHorarioRepository->getErrorTxt();
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}

(new JsonResponse($jsondata))->send();
exit();

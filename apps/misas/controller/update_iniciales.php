<?php

// INICIO Cabecera global de URL de controlador *********************************

use Illuminate\Http\JsonResponse;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\InicialesSacdRepository;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');
$Qiniciales = (string)filter_input(INPUT_POST, 'iniciales');
$Qcolor = (string)filter_input(INPUT_POST, 'color');

$error_txt = '';

$InicialesSacdRepository = new InicialesSacdRepository();
$InicialesSacd = $InicialesSacdRepository->findById($Qid_sacd);
if (is_null($InicialesSacd)) {
    $InicialesSacd = new InicialesSacd();
    $InicialesSacd->setId_nom($Qid_sacd);
}
$InicialesSacd->setIniciales($Qiniciales);
$InicialesSacd->setColor($Qcolor);

if ($InicialesSacdRepository->Guardar($InicialesSacd) === FALSE) {
    $error_txt .= $InicialesSacdRepository->getErrorTxt();
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}

(new JsonResponse($jsondata))->send();
exit();

<?php

use Illuminate\Http\JsonResponse;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//$gestorPersonaSacd = new GestorPersonaSacd();

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');

$error_txt = '';

$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aWhere['id_nom'] = $Qid_sacd;
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
if (empty ($cZonaSacd)) {
    $error_txt .= _("No existe");
} else {
    $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
    $oPersona = $PersonaSacdRepository->findById($Qid_sacd);
    $jsondata['nombre_sacd'] = empty($oPersona->getNombreApellidos()) ? '?' : $oPersona->getNombreApellidos();

    $oZonaSacd = $cZonaSacd[0];
    $jsondata['dw1'] = $oZonaSacd->isDw1();
    $jsondata['dw2'] = $oZonaSacd->isDw2();
    $jsondata['dw3'] = $oZonaSacd->isDw3();
    $jsondata['dw4'] = $oZonaSacd->isDw4();
    $jsondata['dw5'] = $oZonaSacd->isDw5();
    $jsondata['dw6'] = $oZonaSacd->isDw6();
    $jsondata['dw7'] = $oZonaSacd->isDw7();
}

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}

(new JsonResponse($jsondata))->send();
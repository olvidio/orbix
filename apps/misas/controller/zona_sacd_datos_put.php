<?php


// INICIO Cabecera global de URL de controlador *********************************
use Illuminate\Http\JsonResponse;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//$gestorPersonaSacd = new GestorPersonaSacd();

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qdw1 = (string)filter_input(INPUT_POST, 'dw1');
$Qdw2 = (string)filter_input(INPUT_POST, 'dw2');
$Qdw3 = (string)filter_input(INPUT_POST, 'dw3');
$Qdw4 = (string)filter_input(INPUT_POST, 'dw4');
$Qdw5 = (string)filter_input(INPUT_POST, 'dw5');
$Qdw6 = (string)filter_input(INPUT_POST, 'dw6');
$Qdw7 = (string)filter_input(INPUT_POST, 'dw7');

$error_txt = '';

$aWhere = [];
$aWhere['id_zona'] = $Qid_zona;
$aWhere['id_nom'] = $Qid_sacd;
$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
if (empty ($cZonaSacd)) {
    $error_txt .= _("No existe");
} else {
    $oZonaSacd = $cZonaSacd[0];
    $oZonaSacd->DBCarregar();
    $oZonaSacd->setDw1($Qdw1);
    $oZonaSacd->setDw2($Qdw2);
    $oZonaSacd->setDw3($Qdw3);
    $oZonaSacd->setDw4($Qdw4);
    $oZonaSacd->setDw5($Qdw5);
    $oZonaSacd->setDw6($Qdw6);
    $oZonaSacd->setDw7($Qdw7);
    $oZonaSacd->DBGuardar();
      if ($oZonaSacd->DBGuardar() === FALSE) {
        $error_txt .= $oZonaSacd->getErrorTxt();
    }
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
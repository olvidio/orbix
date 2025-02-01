<?php


// INICIO Cabecera global de URL de controlador *********************************
use Illuminate\Http\JsonResponse;
use personas\model\entity\PersonaSacd;
use personas\model\entity\PersonaEx;
use zonassacd\model\entity\GestorZonaSacd;

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
$GesZonasSacd = new GestorZonaSacd();
$cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere);
if (empty ($cZonaSacd)) {
    $error_txt .= _("No existe");
} else {
    if ($Qid_sacd>0) {
        $oPersona = new PersonaSacd($Qid_sacd);
        $jsondata['nombre_sacd'] = empty($oPersona->getNombreApellidos())? '?' : $oPersona->getNombreApellidos();
    } else {
        $oPersona = new PersonaEx($Qid_sacd);
        $jsondata['nombre_sacd'] = empty($oPersona->getNombreApellidos())? '?' : $oPersona->getNombreApellidos();
    }

    $oZonaSacd = $cZonaSacd[0];
    $jsondata['dw1'] = $oZonaSacd->getDw1();
    $jsondata['dw2'] = $oZonaSacd->getDw2();
    $jsondata['dw3'] = $oZonaSacd->getDw3();
    $jsondata['dw4'] = $oZonaSacd->getDw4();
    $jsondata['dw5'] = $oZonaSacd->getDw5();
    $jsondata['dw6'] = $oZonaSacd->getDw6();
    $jsondata['dw7'] = $oZonaSacd->getDw7();
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
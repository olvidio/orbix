<?php
// INICIO Cabecera global de URL de controlador *********************************

use certificados\domain\repositories\CertificadoRepository;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
}

$error_txt = '';
if (!empty($Qid_item)) {
    $CertificadoRepository = new CertificadoRepository();
    $oCertificado = $CertificadoRepository->findById($Qid_item);
    if (!empty($oCertificado)) {
        $certificado = $oCertificado->getCertificado();
        if ($CertificadoRepository->Eliminar($oCertificado) === FALSE) {
            $error_txt .= $CertificadoRepository->getErrorTxt();
        }
        // Hay que borrar también el certificado de las notas_otra_region_stgr
        // Se supone que si accedo a esta página es porque soy una región del stgr.
        $esquema_region_stgr = $_SESSION['session_auth']['esquema'];
        $gesPersonaNotaOtraRegionStgr = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $gesPersonaNotaOtraRegionStgr->deleteCertificado($certificado);
    }
} else {
    $error_txt = _("No se encuentra el certificado");
}

if (!empty($error_txt)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();
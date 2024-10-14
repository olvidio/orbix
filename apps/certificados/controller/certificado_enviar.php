<?php

// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\repositories\CertificadoRepository;
use core\DBPropiedades;
use personas\model\entity\TrasladoDl;
use ubis\model\entity\GestorDelegacion;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

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

$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
$destino = $oCertificado->getDestino();
$certificado = $oCertificado->getCertificado();

$error_txt = '';
// destino?
$oPersona = personas\model\entity\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $error_txt .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
}
$nom = $oPersona->getNombreApellidos();

$dl_origen = core\ConfigGlobal::mi_delef();
$dl_destino = $oPersona->getDl();
$gesDelegacion = new GestorDelegacion();
$a_datos_region_stgr = $gesDelegacion->mi_region_stgr($dl_destino);
$esquema_region_stgr_dst =$a_datos_region_stgr['esquema_region_stgr'];

//1.- saber si está en aquinate
// comprobar que no es una dl que ya tiene su esquema
$oDBPropiedades = new DBPropiedades();
$a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
$is_dl_in_orbix = FALSE;
foreach ($a_posibles_esquemas as $esquema) {
    $row = explode('-', $esquema);
    if ($row[1] === $dl_destino) {
        $is_dl_in_orbix = TRUE;
        break;
    }
}

//2.- mover $certificado
if ($is_dl_in_orbix) {
    $oTrasladoDl = new TrasladoDl();
    $oTrasladoDl->setReg_dl_dst($esquema_region_stgr_dst);

    $oTrasladoDl->trasladar_certificados($oCertificado);
    $error_txt = $oTrasladoDl->getError();
    //3.- enviar aviso

} else {
    $error_txt .= _("Hay que enviar manualmente el certificado. Esta persona no está en aquinate");
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
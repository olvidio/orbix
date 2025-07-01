<?php


// INICIO Cabecera global de URL de controlador *********************************
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;
use src\certificados\application\repositories\CertificadoRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$Qpdf = $_POST['pdf'];

$pdf_content = base64_decode($Qpdf);

$error_txt = '';

$certificadoRepository = new CertificadoRepository();

$oCertificado = $certificadoRepository->findById($Qid_item);
$oCertificado->setId_nom($Qid_nom);
$oCertificado->setDocumento($pdf_content);
if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
    $error_txt .= $certificadoRepository->getErrorTxt();
}
// también hay que guardarlo en las notas afectadas
$oF_certificado = $oCertificado->getF_certificado();
// Se supone que si accedo a esta página es porque soy una región del stgr.
$esquema_region_stgr = $_SESSION['session_auth']['esquema'];
$gesPersonaNotaOtraRegionStgr = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
$gesPersonaNotaOtraRegionStgr->addCertificado($Qid_nom, $Qcertificado, $oF_certificado);


$data['mensaje'] = 'ok';
$data['item'] = $Qid_item;

ContestarJson::enviar($error_txt, $data);
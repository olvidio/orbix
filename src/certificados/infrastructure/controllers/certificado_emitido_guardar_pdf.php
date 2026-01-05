<?php

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use web\ContestarJson;

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$Qpdf = $_POST['pdf'];

$pdf_content = base64_decode($Qpdf);
$certificado = base64_decode($Qcertificado);

$error_txt = '';

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);

$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
$oCertificadoEmitido->setId_nom($Qid_nom);
$oCertificadoEmitido->setDocumento($pdf_content);
if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
    $error_txt .= $certificadoEmitidoRepository->getErrorTxt();
}
// también hay que guardarlo en las notas afectadas
$oF_certificado = $oCertificadoEmitido->getF_certificado();
// Se supone que si accedo a esta página es porque soy una región del stgr.
$esquema_region_stgr = $_SESSION['session_auth']['esquema'];
$PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
try {
    $PersonaNotaOtraRegionStgrRepository->addCertificado($Qid_nom, $certificado, $oF_certificado);
} catch (\Exception $e) {
    $error_txt .= $e->getMessage();
}

$data['mensaje'] = 'ok';
$data['item'] = $Qid_item;

ContestarJson::enviar($error_txt, $data);
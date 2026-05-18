<?php

use src\certificados\application\CertificadoEmitidoGuardarMessages;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$Qpdf = $_POST['pdf'];

$pdf_content = base64_decode($Qpdf);
$certificado = base64_decode($Qcertificado);

$error_txt = '';

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);

$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
if ($oCertificadoEmitido === null) {
    $error_txt .= '<br>' . sprintf(_('No encuentro certificado emitido con id_item: %d'), $Qid_item);
    ContestarJson::enviar($error_txt, []);
    return;
}

$oCertificadoEmitido->setId_nom($Qid_nom);
$oCertificadoEmitido->setDocumento($pdf_content);
try {
    if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
        $error_txt .= CertificadoEmitidoGuardarMessages::fromDatabaseError(
            (string)$certificadoEmitidoRepository->getErrorTxt()
        );
    }
} catch (\Throwable $e) {
    $error_txt .= CertificadoEmitidoGuardarMessages::fromThrowable($e);
}

if ($error_txt === '') {
    // también hay que guardarlo en las notas afectadas
    $oF_certificado = $oCertificadoEmitido->getF_certificado();
    $esquema_region_stgr = $_SESSION['session_auth']['esquema'] ?? '';
    try {
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionStgrRepository->addCertificado($Qid_nom, $certificado, $oF_certificado);
    } catch (\Throwable $e) {
        $error_txt .= $e->getMessage();
    }
}

$data = ['mensaje' => 'ok', 'item' => $Qid_item];

ContestarJson::enviar($error_txt, $data);
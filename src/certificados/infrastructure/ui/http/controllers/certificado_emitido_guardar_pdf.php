<?php

use src\certificados\application\CertificadoEmitidoGuardarMessages;
use src\certificados\application\support\CertificadosSession;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository */
$certificadoEmitidoRepository = DependencyResolver::get(CertificadoEmitidoRepositoryInterface::class);

$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item');
$Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom');
$Qcertificado = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'certificado');
$Qpdf = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pdf');

$pdf_content = base64_decode($Qpdf, true);
$certificado = base64_decode($Qcertificado, true);
$error_txt = '';

$oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
if ($oCertificadoEmitido === null) {
    $error_txt .= '<br>' . sprintf(_('No encuentro certificado emitido con id_item: %d'), $Qid_item);
    ContestarJson::enviar($error_txt, []);
    return;
}

$oCertificadoEmitido->setId_nom($Qid_nom);
$oCertificadoEmitido->setDocumento(is_string($pdf_content) ? $pdf_content : null);
try {
    if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
        $error_txt .= CertificadoEmitidoGuardarMessages::fromDatabaseError(
            $certificadoEmitidoRepository->getErrorTxt(),
        );
    }
} catch (\Throwable $e) {
    $error_txt .= CertificadoEmitidoGuardarMessages::fromThrowable($e);
}

if ($error_txt === '') {
    $oF_certificado = $oCertificadoEmitido->getF_certificado();
    $esquema_region_stgr = CertificadosSession::esquemaRegionStgr();
    try {
        /** @var PersonaNotaOtraRegionStgrRepositoryInterface $personaNotaRepo */
        $personaNotaRepo = DependencyResolver::make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr],
        );
        $personaNotaRepo->addCertificado($Qid_nom, is_string($certificado) ? $certificado : '', $oF_certificado);
    } catch (\Throwable $e) {
        $error_txt .= $e->getMessage();
    }
}

$data = ['mensaje' => 'ok', 'item' => $Qid_item];
ContestarJson::enviar($error_txt, $data);

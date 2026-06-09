<?php

use frontend\shared\helpers\SignedDownloadToken;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;


$tk = (isset($_GET['tk']) && is_scalar($_GET['tk'])) ? trim((string) $_GET['tk']) : '';

$Qid_item = 0;
$parsed = SignedDownloadToken::parse($tk);
if ($parsed !== null && $parsed['s'] === SignedDownloadToken::SCOPE_CERT_RECIBIDO) {
    $Qid_item = (int) ($parsed['id'] ?? 0);
}

if ($Qid_item <= 0) {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(400);
    echo _('Enlace de descarga no válido o caducado.');
    exit;
}

/** @var CertificadoRecibidoRepositoryInterface $certificadoRecibidoRepository */
$certificadoRecibidoRepository = DependencyResolver::get(CertificadoRecibidoRepositoryInterface::class);
$oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);

if ($oCertificadoRecibido === null) {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(404);
    echo _('No se encuentra el certificado.');
    exit;
}

$doc = $oCertificadoRecibido->getDocumento();
if ($doc === null || $doc === '') {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(404);
    echo _('No hay PDF asociado a este certificado.');
    exit;
}

$nombre_fichero = ($oCertificadoRecibido->getCertificado() ?? 'certificado') . '.pdf';
$ctype = 'application/octet-stream';

header('Content-Description: File Transfer');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: private', false);
header('Content-Type: application/force-download');
header('Content-Type: application/download', false);
header('Content-Type: ' . $ctype);
header('Content-disposition: attachment; filename="' . $nombre_fichero . '"');

ob_start();
ob_clean();
flush();
echo $doc;
exit;

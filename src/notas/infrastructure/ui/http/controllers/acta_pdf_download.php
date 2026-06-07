<?php
use src\shared\infrastructure\DependencyResolver;

use frontend\shared\helpers\SignedDownloadToken;
use src\notas\domain\contracts\ActaRepositoryInterface;

require_once 'frontend/shared/global_header_front.inc';

$tkRaw = $_GET['tk'] ?? '';
$tk = is_string($tkRaw) ? trim($tkRaw) : '';

$Qacta = '';
$parsed = SignedDownloadToken::parse($tk);
if ($parsed !== null && $parsed['s'] === SignedDownloadToken::SCOPE_NOTAS_ACTA) {
    $Qacta = (string) ($parsed['a'] ?? '');
}

if ($Qacta === '') {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(400);
    echo _('Enlace de descarga no válido o caducado.');
    exit;
}

$ActaRepository = DependencyResolver::get(ActaRepositoryInterface::class);
$oActa = $ActaRepository->findById($Qacta);

if ($oActa === null) {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(404);
    echo _('No se encuentra el acta.');
    exit;
}

$doc = $oActa->getPdf();
if ($doc === null || $doc === '') {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(404);
    echo _('No hay PDF asociado a este acta.');
    exit;
}

$nombre_fichero = $Qacta . '.pdf';

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

exit();

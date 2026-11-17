<?php

/**
 * Descarga el plano de una dirección. Solo con `tk` ({@see SignedDownloadToken}, alcance `ubis.plano`),
 * que pone el frontend al comprobar que hay plano.
 */

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\SignedDownloadToken;
use src\ubis\application\PlanosDireccion;

$tkRaw = $_GET['tk'] ?? '';
$parsed = SignedDownloadToken::parse(is_string($tkRaw) ? trim($tkRaw) : '');
if ($parsed === null || $parsed['s'] !== SignedDownloadToken::SCOPE_UBIS_PLANO) {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(400);
    echo _("Enlace de descarga no válido o caducado.");
    exit;
}

try {
    $plano = DependencyResolver::get(PlanosDireccion::class)->leer((string) ($parsed['o'] ?? ''), (int) ($parsed['id'] ?? 0));
} catch (InvalidArgumentException) {
    $plano = null;
}
if ($plano === null) {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(404);
    echo _("no existe un plano");
    exit;
}

$ctype = match (strtolower($plano['extension'])) {
    'odt' => 'application/vnd.oasis.opendocument.text',
    'pdf' => 'application/pdf',
    'zip' => 'application/zip',
    'rtf', 'doc' => 'application/msword',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',
    'gif' => 'image/gif',
    'png' => 'image/png',
    'jpeg', 'jpg' => 'image/jpg',
    default => 'application/octet-stream',
};
$nom_ext = $plano['nom'] . ($plano['extension'] !== '' ? '.' . $plano['extension'] : '');

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: ' . $ctype);
header('Content-Disposition: attachment; filename="' . str_replace(['"', "\r", "\n"], '', $nom_ext) . '"');
header('Content-Transfer-Encoding: binary');

ob_start();
ob_clean();
flush();
echo $plano['doc'];

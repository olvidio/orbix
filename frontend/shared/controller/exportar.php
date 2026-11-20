<?php

/**
 * Descarga del bloque de pantalla que el usuario exporta (botones de exportar de las listas).
 *
 * Recibe el formulario `frm_export` de la portada (firmado con `HashF`): tipo (`html`, `html_zip`, `odfc`,
 * `odft`), título, referencia de la vista y el HTML en base64. Exige sesión, como cualquier pantalla.
 */

use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ExportadorDocumento;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$tipo = (string) filter_input(INPUT_POST, 'frm_export_tipo');
$titulo = (string) filter_input(INPUT_POST, 'frm_export_titulo');
$referencia = (string) filter_input(INPUT_POST, 'frm_export_ref');
$documento = (string) ($_POST['frm_export_ex'] ?? '');
// El HTML viaja en base64 para que el cortafuegos de aplicación no lo tome por un ataque.
if ((string) filter_input(INPUT_POST, 'frm_export_enc') === 'b64') {
    $decodificado = base64_decode($documento, true);
    if ($decodificado !== false) {
        $documento = $decodificado;
    }
}
session_write_close();

// Nada de lo que se escriba mientras se genera (avisos de PHP incluidos) va delante del fichero.
ob_start();
try {
    $fichero = (new ExportadorDocumento())->generar($tipo, $titulo, $documento, $referencia);
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    exit(_("Tipo de exportación no admitido"));
} catch (\Throwable $e) {
    error_log('exportar: ' . $e->getMessage());
    http_response_code(500);
    exit(_("No se ha podido generar el fichero"));
}

if (headers_sent()) {
    error_log('exportar: las cabeceras ya se habían enviado');
    exit;
}
while (ob_get_level() > 0) {
    ob_end_clean();
}
$nombreAscii = (string) preg_replace('/[^A-Za-z0-9_.-]/', '_', $fichero['nombre']);
header('Content-Type: ' . $fichero['mime']);
header('Content-Disposition: attachment; filename="' . $nombreAscii . '"; filename*=UTF-8\'\'' . rawurlencode($fichero['nombre']));
header('Content-Length: ' . strlen($fichero['contenido']));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
echo $fichero['contenido'];

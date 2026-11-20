<?php

declare(strict_types=1);

/**
 * Firma una URL de pantalla para la suite E2E, que abre pantallas por su cuenta (`abrirPantalla()`,
 * entradas de menú leídas de la BD) y no puede calcular la firma `h`: la clave solo la tiene el servidor.
 *
 * Solo existe en la instancia de pruebas (`WEBDIR=pruebas`, parámetro del servidor web); en cualquier
 * otra responde 404. Firma como el menú ({@see \frontend\shared\security\HashF::add_hash()}), para la
 * sesión de quien lo pide.
 *
 * POST `ruta` (path de un controlador de `frontend/`, p. ej. `/orbix/frontend/x/controller/y.php`) y
 * `parametros` (query string) → JSON `{query}` con los parámetros firmados.
 */

use frontend\shared\config\OrbixRuntime;
use frontend\shared\FrontBootstrap;
use frontend\shared\security\HashF;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

if (!OrbixRuntime::webdirIsPruebas()) {
    http_response_code(404);
    exit;
}

$ruta = (string) filter_input(INPUT_POST, 'ruta');
$parametros = (string) filter_input(INPUT_POST, 'parametros');

header('Content-Type: application/json; charset=UTF-8');
if (preg_match('~^/[A-Za-z0-9_./-]*/frontend/[A-Za-z0-9_./-]+\.php$~', $ruta) !== 1 || str_contains($ruta, '..')) {
    echo json_encode(['query' => '']);
    exit;
}
parse_str($parametros, $campos);
/** @var array<string, mixed> $campos */
echo json_encode(['query' => HashF::add_hash($campos, $ruta)]);

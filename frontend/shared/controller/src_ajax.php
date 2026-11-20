<?php

declare(strict_types=1);

/**
 * Proxy genérico AJAX hacia endpoints `/src/...`.
 *
 * Fichero físico bajo `frontend/` para que funcione también con prefijos
 * `/orbixsf` (donde las rutas extensionless `/src/*` suelen 404).
 *
 * Destino: PATH_INFO (`…/src_ajax.php/src/modulo/accion`) o, en su defecto,
 * query/POST `_orbix_src=/src/modulo/accion`.
 *
 * @see \frontend\shared\config\AppUrlConfig::srcBrowserUrl()
 */

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;
use src\shared\infrastructure\ui\http\AccesoRolPau;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

$src = src_ajax_resolve_route();
if ($src === null) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ruta src inválida';
    exit(1);
}

// La restricción por rol se comprueba sobre la ruta que se va a despachar, no sobre la URL del proxy.
AccesoRolPau::fijarRutaDespachada($src);
AccesoRolPau::comprobarPeticionActual();
// La firma `h`: si la petición no la trae, tiene que ser de las que se admiten sin ella para esta ruta.
\frontend\shared\security\HashF::exigirFirmaEnRuta($src);

header('Content-Type: application/json; charset=UTF-8');
$payload = PostRequest::requestPayloadForHash();
unset($payload['_orbix_src']);
echo PostRequest::sendRawPost($src, $payload);

/**
 * @return non-empty-string|null
 */
function src_ajax_resolve_route(): ?string
{
    $candidates = [];

    $pathInfo = $_SERVER['PATH_INFO'] ?? '';
    if (is_string($pathInfo) && $pathInfo !== '') {
        $candidates[] = $pathInfo;
    }

    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (is_string($requestUri) && $requestUri !== '') {
        $path = parse_url($requestUri, PHP_URL_PATH);
        if (is_string($path) && preg_match('#src_ajax\.php(/src/.+)$#', $path, $m) === 1) {
            $candidates[] = $m[1];
        }
    }

    foreach (['_orbix_src'] as $key) {
        foreach ([$_GET[$key] ?? null, $_POST[$key] ?? null] as $raw) {
            if (is_string($raw) && $raw !== '') {
                $candidates[] = $raw;
            }
        }
    }

    foreach ($candidates as $raw) {
        $src = '/' . ltrim(rawurldecode($raw), '/');
        $src = strtok($src, '?') ?: $src;
        // Solo `/src/<modulo>/<accion>` (o su forma larga de controlador): sin `..` ni otras rutas del servidor.
        if (preg_match('#^/src/[A-Za-z0-9_]+/(infrastructure/ui/http/controllers/)?[A-Za-z0-9_]+(\.php)?$#', $src) === 1) {
            return $src;
        }
    }

    return null;
}

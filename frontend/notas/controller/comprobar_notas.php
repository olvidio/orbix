<?php

/**
 * Pantalla “comprobar notas”: el SQL y mutaciones corren en
 * {@see src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php}.
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Esta página sirve para comprobar las notas de la tabla e_notas.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$payload = PostRequest::getDataFromUrl(
    '/src/notas/comprobar_notas_page_data',
    PostRequest::requestPayloadForHash()
);

if (!is_array($payload) || !isset($payload['html']) || !is_string($payload['html'])) {
    exit(_('No se pudo cargar la comprobación de notas.'));
}

echo $payload['html'];
echo $oPosicion->mostrar_left_slide(1);

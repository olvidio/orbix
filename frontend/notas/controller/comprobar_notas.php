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
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$requestPayload = PostRequest::requestPayloadForHash();
$forward = [];
$idTabla = $requestPayload['id_tabla'] ?? '';
if (is_string($idTabla) && $idTabla !== '') {
    $forward['id_tabla'] = $idTabla;
}
$actualizar = $requestPayload['actualizar'] ?? '';
if (is_string($actualizar) && $actualizar !== '') {
    $forward['actualizar'] = $actualizar;
}
$idNom = $requestPayload['id_nom'] ?? null;
if (is_int($idNom) || (is_string($idNom) && is_numeric($idNom))) {
    $forward['id_nom'] = (int) $idNom;
}
$idAsignatura = $requestPayload['id_asignatura'] ?? '';
if (is_string($idAsignatura) && $idAsignatura !== '') {
    $forward['id_asignatura'] = $idAsignatura;
}
if (isset($requestPayload['dl']) && is_array($requestPayload['dl'])) {
    $forward['dl'] = $requestPayload['dl'];
}

$payload = PostRequest::getDataFromUrl(
    '/src/notas/comprobar_notas_page_data',
    $forward
);

if (!isset($payload['html']) || !is_string($payload['html'])) {
    exit(_('No se pudo cargar la comprobación de notas.'));
}

echo $payload['html'];
echo $oPosicion->mostrar_left_slide(1);

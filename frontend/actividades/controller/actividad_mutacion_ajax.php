<?php
/**
 * Proxy AJAX frontend → `/src/actividades/actividad_{nuevo,editar,cambiar_tipo}`.
 *
 * El formulario lleva hash de {@see HashFront::getCamposHtml} pensado para la pantalla
 * frontend (p. ej. planning_casa_modificar). Un POST directo a `/src/...` repasa
 * `after_global_object.inc` y el hash no cuadra → 302 a index.php (el cliente ve HTML).
 *
 * Aquí {@see FrontBootstrap::boot()} valida el hash del formulario; {@see PostRequest}
 * reenvía in-process al backend con firma nueva. Siempre responde JSON.
 */

declare(strict_types=1);

use frontend\actividades\helpers\ActividadesMutacionSupport;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/ActividadesMutacionSupport.php';

// Filtrar $_POST antes de validar el hash en boot().
ActividadesMutacionSupport::mutacionAjaxSanitizePost();

FrontBootstrap::boot();

$mod = isset($_POST['mod']) && is_scalar($_POST['mod']) ? (string) $_POST['mod'] : '';
$endpoints = [
    'nuevo' => '/src/actividades/actividad_nuevo',
    'editar' => '/src/actividades/actividad_editar',
    'cambiar_tipo' => '/src/actividades/actividad_cambiar_tipo',
];

if (!isset($endpoints[$mod])) {
    AjaxJsonSupport::response(_('modo no válido'));
}

/** @var array<string, mixed> $campos */
$campos = $_POST;

$data = PostRequest::getDataFromUrl($endpoints[$mod], $campos, false);
if (!empty($data['error'])) {
    $msg = PostRequest::stripInternalCallProvenance(PayloadCoercion::string($data['error']));
    $msg = html_entity_decode(strip_tags($msg), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $msg = trim(preg_replace('/\s+/', ' ', $msg) ?? '');

    AjaxJsonSupport::response($msg !== '' ? $msg : _('Error al guardar'));
}

AjaxJsonSupport::response();

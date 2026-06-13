<?php
/**
 * Pantalla de peticiones de plaza de una persona (n / a / agd).
 *
 * Obtiene la lista de actividades candidatas + peticiones actuales
 * de `/src/actividadplazas/peticiones_activ_data` y monta el
 * `frontend\shared\web\DesplegableArray` para editar. Guardar/borrar se hacen via
 * AJAX contra `/src/actividadplazas/peticiones_{guardar,eliminar}`.
 *
 * Migrada desde `apps/actividadplazas/controller/peticiones_activ.php` +
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php` siguiendo
 * `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\DesplegableArray;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once 'frontend/actividadplazas/helpers/actividadplazas_support.php';

$oPosicion = FrontBootstrap::boot();
$Qtodos = (int)filter_input(INPUT_POST, 'todos');

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());

$stack2 = actividadplazas_stack_from_post();
if ($stack2 !== null) {
    $oPosicion2 = new Posicion();
    if ($oPosicion2->goStack($stack2)) {
        $oPosicion2->olvidar($stack2);
    }
}

$selParts = actividadplazas_sel_hash_parts();
if ($selParts !== null) {
    $Qid_nom = tessera_imprimir_int($selParts['first']);
    $Qna = $selParts['second'];
    $Qsactividad = (string)(filter_input(INPUT_POST, 'sactividad') ?: filter_input(INPUT_POST, 'que'));
    $Qtodos = empty($Qtodos) ? 1 : $Qtodos;
} else {
    $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qsactividad = (string)(filter_input(INPUT_POST, 'sactividad') ?: filter_input(INPUT_POST, 'que'));
}

$campos = [
    'id_nom' => $Qid_nom,
    'na' => $Qna,
    'sactividad' => $Qsactividad,
    'todos' => $Qtodos,
    'id_ctr_agd' => (int)filter_input(INPUT_POST, 'id_ctr_agd'),
    'id_ctr_n' => (int)filter_input(INPUT_POST, 'id_ctr_n'),
];

$payload = actividadplazas_gestion_plazas_from_payload(
    PostRequest::getDataFromUrl('/src/actividadplazas/peticiones_activ_data', $campos)
);

$ap_nom = $payload['ap_nom'];
$sid_activ = $payload['sid_activ'];
$aOpciones = $payload['opciones'];
$Qsactividad = $payload['sactividad'] !== '' ? $payload['sactividad'] : $Qsactividad;
$Qna = $payload['na'] !== '' ? $payload['na'] : $Qna;

$oSelects = new DesplegableArray($sid_activ, $aOpciones, 'actividades');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_actividades(event)');

$stack = $oPosicion->getStack(0);

$oHash = new HashFront();
$oHash->setCamposForm('actividades!actividades_mas!actividades_num');
$oHash->setcamposNo('que!actividades');
$oHash->setArraycamposHidden([
    'id_nom' => $Qid_nom,
    'na' => $Qna,
    'sactividad' => $Qsactividad,
    'que' => '',
    'stack' => $stack,
]);

$apiBase = AppUrlConfig::getApiBaseUrl();
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHashLocal = new HashFront();
    $oHashLocal->setUrl($url);
    $oHashLocal->setCamposForm($campos);
    return $url . $oHashLocal->linkSinVal();
};
$url_guardar = $buildHashedUrl(
    $apiBase . '/src/actividadplazas/peticiones_guardar',
    'id_nom!sactividad!actividades!actividades_mas!actividades_num'
);
$url_eliminar = $buildHashedUrl(
    $apiBase . '/src/actividadplazas/peticiones_eliminar',
    'id_nom!sactividad'
);

$txt_guardar = _("guardar peticiones");

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oSelects' => $oSelects,
    'ap_nom' => $ap_nom,
    'txt_guardar' => $txt_guardar,
    'url_guardar' => $url_guardar,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('peticiones_activ.phtml', $a_campos);

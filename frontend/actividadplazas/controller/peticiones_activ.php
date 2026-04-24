<?php
/**
 * Pantalla de peticiones de plaza de una persona (n / a / agd).
 *
 * Obtiene la lista de actividades candidatas + peticiones actuales
 * de `/src/actividadplazas/peticiones_activ_data` y monta el
 * `web\DesplegableArray` para editar. Guardar/borrar se hacen via
 * AJAX contra `/src/actividadplazas/peticiones_{guardar,eliminar}`.
 *
 * Migrada desde `apps/actividadplazas/controller/peticiones_activ.php` +
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php` siguiendo
 * `refactor.md`.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use web\DesplegableArray;
use web\Hash;
use web\Posicion;

require_once 'frontend/shared/global_header_front.inc';

$Qtodos = (int)filter_input(INPUT_POST, 'todos');

$oPosicion->recordar();
if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack2)) {
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_nom = (int)strtok($a_sel[0], '#');
    $Qna = strtok('#');
    $Qsactividad = (string)filter_input(INPUT_POST, 'que');
    $Qtodos = empty($Qtodos) ? 1 : $Qtodos;
} else {
    $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
}

$campos = [
    'id_nom' => $Qid_nom,
    'na' => $Qna,
    'sactividad' => $Qsactividad,
    'todos' => $Qtodos,
    'id_ctr_agd' => (int)filter_input(INPUT_POST, 'id_ctr_agd'),
    'id_ctr_n' => (int)filter_input(INPUT_POST, 'id_ctr_n'),
];

$data = PostRequest::getDataFromUrl('/src/actividadplazas/peticiones_activ_data', $campos);
$payload = is_array($data) && isset($data['data']) && is_array($data['data']) ? $data['data'] : [];

$ap_nom = (string)($payload['ap_nom'] ?? '');
$sid_activ = (string)($payload['sid_activ'] ?? '');
$aOpciones = $payload['opciones'] ?? [];
$Qsactividad = (string)($payload['sactividad'] ?? $Qsactividad);
$Qna = (string)($payload['na'] ?? $Qna);

$oSelects = new DesplegableArray($sid_activ, $aOpciones, 'actividades');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_actividades(event)');

$stack = $oPosicion->getStack(0);

$oHash = new Hash();
$oHash->setCamposForm('actividades!actividades_mas!actividades_num');
$oHash->setcamposNo('que!actividades');
$oHash->setArraycamposHidden([
    'id_nom' => $Qid_nom,
    'na' => $Qna,
    'sactividad' => $Qsactividad,
    'que' => '',
    'stack' => $stack,
]);

$web = rtrim(ConfigGlobal::getWeb(), '/');
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHashLocal = new Hash();
    $oHashLocal->setUrl($url);
    $oHashLocal->setCamposForm($campos);
    return $url . $oHashLocal->linkSinVal();
};
$url_guardar = $buildHashedUrl(
    $web . '/src/actividadplazas/peticiones_guardar',
    'id_nom!sactividad!actividades!actividades_mas!actividades_num'
);
$url_eliminar = $buildHashedUrl(
    $web . '/src/actividadplazas/peticiones_eliminar',
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

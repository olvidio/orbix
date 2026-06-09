<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

/**
 * Informe anual STGR - Numerarios (puntos 1..18 + `x`).
 *
 * Orquestacion delgada: delega el calculo en `/src/notas/informe_stgr_n_data`
 * y renderiza la tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$QdlRaw = filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qdl = is_array($QdlRaw) ? $QdlRaw : [];
$Qlista = (string)filter_input(INPUT_POST, 'lista');

$payload = PostRequest::getDataFromUrl('/src/notas/informe_stgr_n_data', [
    'dl' => $Qdl,
    'lista' => $Qlista,
]);

$ce_lugar = tessera_imprimir_string($payload['ce_lugar'] ?? '');
unset($payload['ce_lugar']);

if ($ce_lugar === '') {
    echo _("No está definido el ce para esta dl/r");
    echo '<br>';
    echo _("Hay definirlo en los parámetros de configuración.");
    echo '<br>';
}

$datos = $payload;

$a_campos = [
    'titulo' => strtoupper_dlb(_("alumnos numerarios")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);

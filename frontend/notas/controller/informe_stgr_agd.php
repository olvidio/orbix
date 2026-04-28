<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

use function frontend\shared\helpers\strtoupper_dlb;

/**
 * Informe anual STGR - Agregados (puntos 21..33 + `x`).
 *
 * Orquestacion delgada: delega el calculo en `/src/notas/informe_stgr_agd_data`
 * y renderiza la tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/global_header_front.inc';

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qlista = (string)filter_input(INPUT_POST, 'lista');

$datos = PostRequest::getDataFromUrl('/src/notas/informe_stgr_agd_data', [
    'dl' => $Qdl,
    'lista' => $Qlista,
]);
$datos = is_array($datos) ? $datos : [];

$a_campos = [
    'titulo' => strtoupper_dlb(_("alumnos agregados")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);

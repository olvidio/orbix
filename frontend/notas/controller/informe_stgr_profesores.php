<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

/**
 * Informe anual STGR - Profesores (puntos 36..47).
 *
 * Orquestacion delgada: delega el calculo en `/src/notas/informe_stgr_profesores_data`
 * y renderiza la tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qlista = (string)filter_input(INPUT_POST, 'lista');

$datos = PostRequest::getDataFromUrl('/src/notas/informe_stgr_profesores_data', [
    'lista' => $Qlista,
]);
$datos = is_array($datos) ? $datos : [];

$a_campos = [
    'titulo' => strtoupper_dlb(_("profesores stgr")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);

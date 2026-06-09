<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Listado de exigencias de atencion por centros / iglesias
 * (cr 9/05, Anexo2, 9.4 b).
 *
 * Obtiene los datos de `/src/encargossacd/listas_exigencia_ctr_data`
 * ({@see \src\encargossacd\application\ListasExigenciaCtrData}) y renderiza
 * la vista generica `listas.phtml`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsf = (int)filter_input(INPUT_POST, 'sf');
$Qctr_igl = (string)filter_input(INPUT_POST, 'ctr_igl');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_exigencia_ctr_data', [
    'sf' => $Qsf,
    'ctr_igl' => $Qctr_igl,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'cabecera_left' => (string)($datos['cabecera_left'] ?? ''),
    'cabecera_right' => (string)($datos['cabecera_right'] ?? ''),
    'cabecera_right_2' => (string)($datos['cabecera_right_2'] ?? ''),
    'Html' => (string)($datos['Html'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas.phtml', $a_campos);

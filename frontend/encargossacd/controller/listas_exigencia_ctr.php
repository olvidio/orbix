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
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsf = encargossacd_post_int('sf');
$Qctr_igl = encargossacd_post_string('ctr_igl');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_exigencia_ctr_data', [
    'sf' => $Qsf,
    'ctr_igl' => $Qctr_igl,
]);

$listaCampos = encargossacd_listas_campos_from_payload($datos);
$a_campos = ['oPosicion' => $oPosicion] + $listaCampos;

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas.phtml', $a_campos);

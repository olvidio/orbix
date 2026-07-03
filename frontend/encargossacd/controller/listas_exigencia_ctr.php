<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;
use frontend\encargossacd\helpers\EncargossacdPayload;

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

$Qsf = EncargossacdPostInput::postInt('sf');
$Qctr_igl = EncargossacdPostInput::postString('ctr_igl');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_exigencia_ctr_data', [
    'sf' => $Qsf,
    'ctr_igl' => $Qctr_igl,
]);

$listaCampos = EncargossacdPayload::listasCamposFromPayload($datos);
$a_campos = ['oPosicion' => $oPosicion] + $listaCampos;

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas.phtml', $a_campos);

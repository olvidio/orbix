<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;
use frontend\encargossacd\helpers\EncargossacdPayload;

/**
 * Listado de atencion SACD segun cr 9/05, Anexo2, 9.4 a).
 *
 * Capa frontend del slice. Obtiene los datos de
 * `/src/encargossacd/listas_a_data`
 * ({@see \src\encargossacd\application\ListasAData}) y los pasa a la vista
 * generica `listas.phtml`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsf = EncargossacdPostInput::postInt('sf');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_a_data', ['sf' => $Qsf]);

$listaCampos = EncargossacdPayload::listasCamposFromPayload($datos);
$a_campos = ['oPosicion' => $oPosicion] + $listaCampos;

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas.phtml', $a_campos);

<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($data['a_opciones']);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setBlanco(0);

$url_ajax_lista = 'frontend/zonassacd/controller/zona_sacd_lista_ajax.php';
$url_ajax_update = 'frontend/zonassacd/controller/zona_sacd_update_ajax.php';

$oHashSacd = new Hash();
$oHashSacd->setUrl($url_ajax_lista);
$oHashSacd->setCamposForm('id_zona');
$h_sacd = $oHashSacd->linkSinVal();

$oHashUrlGet = new Hash();
$oHashUrlGet->setUrl('apps/misas/controller/zona_sacd_datos_get.php');
$oHashUrlGet->setCamposForm('id_sacd!id_zona');
$h_url_get = $oHashUrlGet->linkSinVal();

$oHashUrlPut = new Hash();
$oHashUrlPut->setUrl('apps/misas/controller/zona_sacd_datos_put.php');
$oHashUrlPut->setCamposForm('id_sacd!id_zona!dw1!dw2!dw3!dw4!dw5!dw6!dw7');
$h_url_put = $oHashUrlPut->linkSinVal();

$oHash = new Hash();
$oHash->setUrl($url_ajax_update);
$oHash->setArraycamposHidden(['acumular' => 0]);
$oHash->setCamposForm('id_zona!id_zona_new');
$oHash->setCamposNo('acumular!scroll_id!sel');

$a_campos = [
    'oHash' => $oHash,
    'url_ajax_lista' => $url_ajax_lista,
    'url_ajax_update' => $url_ajax_update,
    'h_sacd' => $h_sacd,
    'h_url_get' => $h_url_get,
    'h_url_put' => $h_url_put,
    'perm_des' => !empty($data['perm_des']),
    'oDesplZonas' => $oDesplZonas,
];

$oView = new ViewNewPhtml('frontend\zonassacd\controller');
$oView->renderizar('zona_sacd.phtml', $a_campos);

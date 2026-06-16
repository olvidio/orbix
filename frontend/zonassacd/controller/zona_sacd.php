<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/zonassacd/helpers/zonassacd_support.php';

FrontBootstrap::boot();
$page = zonassacd_page_from_payload(PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd'));

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($page['a_opciones']);
$oDesplZonas->setBlanco(false);

$url_ajax_lista = 'frontend/zonassacd/controller/zona_sacd_lista_ajax.php';
$url_ajax_update = 'frontend/zonassacd/controller/zona_sacd_update_ajax.php';

$oHashSacd = new HashFront();
$oHashSacd->setUrl($url_ajax_lista);
$oHashSacd->setCamposForm('id_zona');
$h_sacd = $oHashSacd->linkSinValParams();

$url_zona_sacd_get = 'frontend/zonassacd/controller/zona_sacd_datos_get_ajax.php';
$url_zona_sacd_put = 'frontend/zonassacd/controller/zona_sacd_datos_put_ajax.php';

$oHashUrlGet = new HashFront();
$oHashUrlGet->setUrl($url_zona_sacd_get);
$oHashUrlGet->setCamposForm('id_sacd!id_zona');
$h_url_get = $oHashUrlGet->linkSinValParams();

$oHashUrlPut = new HashFront();
$oHashUrlPut->setUrl($url_zona_sacd_put);
$oHashUrlPut->setCamposForm('id_sacd!id_zona!dw1!dw2!dw3!dw4!dw5!dw6!dw7');
$h_url_put = $oHashUrlPut->linkSinValParams();

$oHash = new HashFront();
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
    'url_zona_sacd_get' => $url_zona_sacd_get,
    'url_zona_sacd_put' => $url_zona_sacd_put,
    'perm_des' => $page['perm_des'],
    'oDesplZonas' => $oDesplZonas,
];

$oView = new ViewNewPhtml('frontend\zonassacd\controller');
$oView->renderizar('zona_sacd.phtml', $a_campos);

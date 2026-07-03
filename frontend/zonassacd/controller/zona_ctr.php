<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\zonassacd\helpers\ZonassacdPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$page = ZonassacdPayload::pageFromPayload(PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr'));

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($page['a_opciones']);
$oDesplZonas->setBlanco(false);

$url_ajax_lista = 'frontend/zonassacd/controller/zona_ctr_lista_ajax.php';
$url_ajax_update = 'frontend/zonassacd/controller/zona_ctr_update_ajax.php';
$oHashCtr = new HashFront();
$oHashCtr->setUrl($url_ajax_lista);
$oHashCtr->setCamposForm('id_zona');
$h_ctr = $oHashCtr->linkSinValParams();

$oHash = new HashFront();
$oHash->setUrl($url_ajax_update);
$oHash->setCamposForm('id_zona_new');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = [
    'oHash' => $oHash,
    'url_ajax_lista' => $url_ajax_lista,
    'url_ajax_update' => $url_ajax_update,
    'h_ctr' => $h_ctr,
    'perm_des' => $page['perm_des'],
    'oDesplZonas' => $oDesplZonas,
];

$oView = new ViewNewPhtml('frontend\zonassacd\controller');
$oView->renderizar('zona_ctr.phtml', $a_campos);

<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($data['a_opciones']);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setBlanco(0);

$url_ajax = 'frontend/zonassacd/controller/zona_ctr_ajax.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ajax);
$oHashCtr->setCamposForm('que!id_zona');
$h_ctr = $oHashCtr->linkSinVal();

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$oHash->setArraycamposHidden(['que' => 'update']);
$oHash->setCamposForm('id_zona_new');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = [
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_ctr' => $h_ctr,
    'perm_des' => !empty($data['perm_des']),
    'oDesplZonas' => $oDesplZonas,
];

$oView = new ViewNewPhtml('frontend\zonassacd\controller');
$oView->renderizar('zona_ctr.phtml', $a_campos);

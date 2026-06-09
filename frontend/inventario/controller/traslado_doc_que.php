<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();

$url_backend = '/src/inventario/lista_de_ctr';
$data = PostRequest::getDataFromUrl($url_backend);
$payload = inventario_post_payload($data);
$a_opciones = inventario_desplegable_opciones($payload['a_opciones'] ?? []);

$oDesplUbis = new Desplegable('id_ubi', $a_opciones, '', true);
$oDesplUbis->setAction('fnjs_busca_lugares_origen()');
$oDesplUbisNew = new Desplegable('id_ubi_new', $a_opciones, '', true);
$oDesplUbisNew->setAction('fnjs_busca_lugares_destino()');

$oHash = new HashFront();
$oHash->setCamposForm('id_ubi!id_ubi_new!sel');
$oHash->setCamposNo('sel!id_lugar!id_lugar_new');

$oHashLugar = new HashFront();
$oHashLugar->setUrl(rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/src/inventario/lista_lugares_de_ubi');
$oHashLugar->setCamposForm('id_ubi');
$h_lugar = $oHashLugar->linkSinValParams();

$a_campos = [
    'oHash' => $oHash,
    'oDesplUbis' => $oDesplUbis,
    'oDesplUbisNew' => $oDesplUbisNew,
    'h_lugar' => $h_lugar,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('traslado_doc_que.phtml', $a_campos);

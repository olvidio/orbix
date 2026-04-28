<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/lista_de_ctr';
$data = PostRequest::getDataFromUrl($url_backend);

$a_opciones = $data['a_opciones'];

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


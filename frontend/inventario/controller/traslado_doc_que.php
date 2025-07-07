<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


// muestra los ctr que tienen el documento.
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/lista_de_ctr.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];

$oDesplUbis = new Desplegable('id_ubi', $a_opciones, '', true);
$oDesplUbis->setAction('fnjs_busca_lugares_origen()');
$oDesplUbisNew = new Desplegable('id_ubi_new', $a_opciones, '', true);
$oDesplUbisNew->setAction('fnjs_busca_lugares_destino()');

$oHash = new Hash();
$oHash->setCamposForm('id_ubi!id_ubi_new!sel');
$oHash->setCamposNo('sel!id_lugar!id_lugar_new');

$oHashLugar = new Hash();
$oHashLugar->setUrl(ConfigGlobal::getWeb(). '/src/inventario/infrastructure/controllers/lista_lugares_de_ubi.php');
$oHashLugar->setCamposForm('id_ubi');
$h_lugar = $oHashLugar->linkSinVal();

$a_campos = [
    'oHash' => $oHash,
    'oDesplUbis' => $oDesplUbis,
    'oDesplUbisNew' => $oDesplUbisNew,
    'h_lugar' => $h_lugar,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('traslado_doc_que.phtml', $a_campos);


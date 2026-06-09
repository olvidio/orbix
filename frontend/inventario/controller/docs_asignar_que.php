<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (int)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (int)filter_input(INPUT_POST, 'id_tipo_doc');

$oPosicion->recordar();

// muestra los ctr que NO tienen el documento.
$url_backend = '/src/inventario/lista_tipo_doc';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = $data['a_opciones'];

$oDesplTipoDoc = new Desplegable('', $aOpciones, '', true);
$oDesplTipoDoc->setNombre('id_tipo_doc');
if (!empty($Qid_tipo_doc)) {
    $oDesplTipoDoc->setOpcion_sel($Qid_tipo_doc);
}

//11
$url_asignados = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_asignado.php?';
//14
$url_no_asignados = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_no_asignado.php?';
//2
$url_ctr = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_de_ctr.php?';
//5
$url_dlb = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_de_dlb.php?';

$oHash = new HashFront();
$oHash->setCamposForm('id_tipo_doc');
$oHash->setArrayCamposHidden(['inventario' => $Qinventario]);

$a_campos = [
    'oHash' => $oHash,
    'inventario' => $Qinventario,
    'oDesplTipoDoc' => $oDesplTipoDoc,
    'url_asignados' => $url_asignados,
    'url_no_asignados' => $url_no_asignados,
    'url_ctr' => $url_ctr,
    'url_dlb' => $url_dlb,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('docs_asignar_que.phtml', $a_campos);


<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qinventario = (int)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (int)filter_input(INPUT_POST, 'id_tipo_doc');

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$url_backend = '/src/inventario/lista_tipo_doc';
$data = PostRequest::getDataFromUrl($url_backend);
$payload = InventarioPayload::postPayload($data);
$aOpciones = InventarioPayload::desplegableOpciones($payload['a_opciones'] ?? []);

$oDesplTipoDoc = new Desplegable('', $aOpciones, '', true);
$oDesplTipoDoc->setNombre('id_tipo_doc');
if ($Qid_tipo_doc !== 0) {
    $oDesplTipoDoc->setOpcion_sel(InventarioPayload::desplegableOpcionSel($Qid_tipo_doc));
}

$url_asignados = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_asignado.php?';
$url_no_asignados = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_no_asignado.php?';
$url_ctr = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_de_ctr.php?';
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

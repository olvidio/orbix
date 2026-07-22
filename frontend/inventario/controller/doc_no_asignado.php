<?php

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\helpers\ListNavSupport;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

$navState = ListNavSupport::mergeSelectionForRecordar(
    array_filter([
        'inventario' => $Qinventario,
        'id_tipo_doc' => $Qid_tipo_doc,
    ], static fn ($v) => $v !== '' && $v !== '0'),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $Qid_tipo_doc > 0 ? ['id_tipo_doc' => $Qid_tipo_doc] : [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, $navState);


// muestra los ctr que NO tienen el documento.
$url_backend = '/src/inventario/lista_docs_no_asignados_por_tipo';
$a_campos_backend = [ 'id_tipo_doc' => $Qid_tipo_doc] ;
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);

$a_cabeceras = ActividadesListaSupport::cabeceras($payload['a_cabeceras'] ?? []);
$a_botones = ActividadesListaSupport::botones($payload['a_botones'] ?? []);
$a_valores = ActividadesListaSupport::datos($payload['a_valores'] ?? []);
$nombreDoc = \frontend\shared\helpers\PayloadCoercion::string($payload['nombreDoc'] ?? '');

$oTabla = new Lista();
$oTabla->setId_tabla('doc_ctr_tabla2');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);


$oHash = new HashFront();
$oHash->setCamposForm('id_tipo_doc');
$oHash->setArrayCamposHidden(['inventario' => $Qinventario]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_no_asignado.phtml', $a_campos);

<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

$oPosicion->recordar();

// muestra los ctr que NO tienen el documento.
$url_backend = '/src/inventario/lista_docs_no_asignados_por_tipo';
$a_campos_backend = [ 'id_tipo_doc' => $Qid_tipo_doc] ;
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);

$a_cabeceras = actividades_lista_cabeceras($payload['a_cabeceras'] ?? []);
$a_botones = actividades_lista_botones($payload['a_botones'] ?? []);
$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);
$nombreDoc = tessera_imprimir_string($payload['nombreDoc'] ?? '');

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

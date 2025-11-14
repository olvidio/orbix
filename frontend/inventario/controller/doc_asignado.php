<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

$oPosicion->recordar();

// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/infrastructure/controllers/lista_docs_asignados_por_tipo.php';
$a_campos_backend = ['id_tipo_doc' => $Qid_tipo_doc];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];
$nombreDoc = $data['nombreDoc'];

$oTabla = new Lista();
$oTabla->setId_tabla('doc_ctr_tabla2');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);


$oHash = new Hash();
$oHash->setCamposForm('id_tipo_doc');
$oHash->setArrayCamposHidden(['inventario' => $Qinventario]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_asignado.phtml', $a_campos);

<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (int)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (int)filter_input(INPUT_POST, 'id_tipo_doc');

$oPosicion->recordar();

// muestra los ctr que NO tienen el documento.
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_tipo_doc.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$aOpciones = $data['a_opciones'];

$oDesplTipoDoc = new Desplegable('',$aOpciones,'',true);
$oDesplTipoDoc->setNombre('id_tipo_doc');
if (!empty($Qid_tipo_doc)) {
    $oDesplTipoDoc->setOpcion_sel($Qid_tipo_doc);
}

//11
$url_asignados = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_asignado.php?';
//14
$url_no_asignados = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_no_asignado.php?';
//2
$url_ctr = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_de_ctr.php?';
//5
$url_dlb = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_de_dlb.php?';

$oHash = new Hash();
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

$oView = new ViewPhtml('../frontend/inventario/controller');
$oView->renderizar('docs_asignar_que.phtml', $a_campos);


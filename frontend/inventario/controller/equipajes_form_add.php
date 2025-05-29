<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_item_egm = (int)filter_input(INPUT_POST, 'id_item_egm');

// posibles tipos de documento
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/lista_tipo_doc.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];

$oDesplTiposDoc = new Desplegable('id_tipo_doc', $a_opciones, '', true);
$oDesplTiposDoc->setAction('fnjs_docs_libres()');

$oHashForm = new Hash();
$oHashForm->setCamposForm('id_tipo_doc!sel');
$oHashForm->setCamposNo('sel');
$oHashForm->setArrayCamposHidden([
    'id_grupo' => $Qid_grupo,
    'id_equipaje' => $Qid_equipaje,
    'id_item_egm' => $Qid_item_egm,
]);

$a_campos = [
    'oHashForm' => $oHashForm,
    'oDesplTiposDoc' => $oDesplTiposDoc,
    'Qid_grupo' => $Qid_grupo,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_form_add.phtml', $a_campos);
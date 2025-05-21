<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');
$Qid_tipo_doc = (string)filter_input(INPUT_POST, 'id_tipo_doc');


// posibles tipos de documento
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_docs_libres.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_equipaje' => $Qid_equipaje, 'id_tipo_doc' => $Qid_tipo_doc]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];

$txt = '<br>';
foreach ($a_valores as $a_valor) {
    $id = $a_valor[0];
    $txt .= "<input class='sel' type='checkbox' name='sel[]' id='a$id' value='$id'>";
    $txt .= $a_valor[1] . ' ' . $a_valor[2];
    $txt .= '<br>';
}
echo $txt;
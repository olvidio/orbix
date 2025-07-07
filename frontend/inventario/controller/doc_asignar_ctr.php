<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$str_selected_id = rawurlencode(json_encode($a_sel));


$oPosicion->recordar();

// muestra los ctr que tienen el documento.
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/lista_docs_asignar_ctr.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'id_tipo_doc' => $Qid_tipo_doc,
    'sel' => $a_sel,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];
$nombreDoc = $data['nombreDoc'];
$isNumerado = $data['isNumerado'];
$sCamposForm = $data['sCamposForm'];


if ($isNumerado) {
    $num_txt = _('número de registro');
} else {
    $num_txt = _('número de ejemplares');
}
$a_cabeceras = array(ucfirst(_("centro")), $num_txt);

$oTabla = new Lista();
$oTabla->setId_tabla('doc_num_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

//9
$url_guardar = ConfigGlobal::getWeb() . '/src/inventario/infrastructure/controllers/doc_asignar_ctr_guardar.php?';

$oHash = new Hash();
$sCamposForm .= "!f_recibido!f_asignado";
$oHash->setCamposForm($sCamposForm);
$oHash->setCamposNo('numerado');
$oHash->setArrayCamposHidden([
    'id_tipo_doc' => $Qid_tipo_doc,
    'numerado' => $isNumerado,
    'str_selected_id' => $str_selected_id,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
    'url_guardar' => $url_guardar,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_asignar_ctr.phtml', $a_campos);

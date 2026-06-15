<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$str_selected_id = rawurlencode((string)json_encode($a_sel));

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$url_backend = '/src/inventario/lista_docs_asignar_ctr';
$a_campos_backend = [
    'id_tipo_doc' => $Qid_tipo_doc,
    'sel' => $a_sel,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);
$view = inventario_doc_asignar_from_payload($payload);

$a_valores = $view['a_valores'];
$nombreDoc = $view['nombreDoc'];
$isNumerado = $view['isNumerado'];
$sCamposForm = $view['sCamposForm'];

if ($isNumerado) {
    $num_txt = _('número de registro');
} else {
    $num_txt = _('número de ejemplares');
}
$a_cabeceras = [ucfirst(_('centro')), $num_txt];

$oTabla = new Lista();
$oTabla->setId_tabla('doc_num_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$url_guardar = AppUrlConfig::getApiBaseUrl() . '/src/inventario/doc_asignar_ctr_guardar?';

$oHash = new HashFront();
$sCamposForm .= '!f_recibido!f_asignado';
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

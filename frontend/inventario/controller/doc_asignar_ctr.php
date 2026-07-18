<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$str_selected_id = rawurlencode((string)json_encode($a_sel));

$navState = ListNavSupport::mergeSelectionForRecordar(
    ['id_tipo_doc' => $Qid_tipo_doc],
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $Qid_tipo_doc > 0 ? ['id_tipo_doc' => $Qid_tipo_doc] : [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, $navState);


$url_backend = '/src/inventario/lista_docs_asignar_ctr';
$a_campos_backend = [
    'id_tipo_doc' => $Qid_tipo_doc,
    'sel' => $a_sel,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$view = InventarioPayload::docAsignarFromPayload($payload);

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

$url_guardar = AppUrlConfig::srcBrowserUrl('/src/inventario/doc_asignar_ctr_guardar') . '?';

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

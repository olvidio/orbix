<?php

use frontend\shared\helpers\PayloadCoercion;
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

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

$aGoBack = [
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc,
];
$navState = ListNavSupport::mergeSelectionForRecordar(
    $aGoBack,
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

$url_backend = '/src/inventario/lista_docs_de_dlb';
$a_campos_backend = [
    'id_tipo_doc' => $Qid_tipo_doc,
    'inventario' => $Qinventario,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$view = InventarioPayload::docDeDlbFromPayload($payload);

$a_valores = $view['a_valores'];
$a_grupos = $view['a_grupos'];
$nombreDoc = \frontend\shared\helpers\PayloadCoercion::string($payload['nombreDoc'] ?? '');

$url_doc_mod = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_asignar_dlb.php?';
$url_imprimir = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_imprimir_dlb.php?';

if (empty($Qinventario)) {
    $a_botones[] = ['txt' => _('Asignar'), 'click' => "fnjs_go(\"$url_doc_mod\")"];
} else {
    $a_botones[] = ['txt' => _('Imprimir dl'), 'click' => " $(\"#dl\").val(\"true\"); fnjs_go(\"$url_imprimir\")"];
    $a_botones[] = ['txt' => _('Imprimir'), 'click' => "fnjs_go(\"$url_imprimir\")"];
}

$a_cabeceras = [ucfirst(_('centro - lugar'))];
$a_botones[] = ['txt' => _('marcar/desmarcar todos'), 'click' => 'fnjs_selectAll("#seleccionados","sel[]","toggle")'];
$a_botones[] = ['txt' => _('marcar todos'), 'click' => 'fnjs_selectAll("#seleccionados","sel[]","all")'];
$a_botones[] = ['txt' => _('desmarcar todos'), 'click' => 'fnjs_selectAll("#seleccionados","sel[]","none")'];

$oTabla = new Lista();
$oTabla->setId_tabla('doc_dlb_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
$oTabla->setGrupos($a_grupos);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'dl' => false,
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc,
]);
$oHash->setCamposNo('dl');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_de_dlb.phtml', $a_campos);

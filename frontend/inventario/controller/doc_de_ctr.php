<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros($aGoBack, list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));

$aGoBack = array(
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc);
$oPosicion->setParametros($aGoBack, 1);

// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/lista_de_ctr_con_docs';
$a_campos_backend = ['id_tipo_doc' => $Qid_tipo_doc, 'inventario' => $Qinventario];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);

$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);
$nombreDoc = tessera_imprimir_string($payload['nombreDoc'] ?? '');

//3
$url_doc_mod = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_asignar_ctr.php?';
//12
$url_imprimir_ctr = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_imprimir_ctr.php?';

if (empty($Qinventario)) {
    $a_botones[] = array('txt' => _("Asignar"), 'click' => "fnjs_go(\"$url_doc_mod\")");
} else {
    $a_botones[] = array('txt' => _('Imprimir dl'), 'click' => " $(\"#dl\").val(\"true\"); fnjs_go(\"$url_imprimir_ctr\")");
    $a_botones[] = array('txt' => _('Imprimir ctr'), 'click' => "fnjs_go(\"$url_imprimir_ctr\")");
}

$a_cabeceras = array(ucfirst(_("centro")));
$a_botones[] = array('txt' => _('marcar/desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\")");
$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");

$oTabla = new Lista();
$oTabla->setId_tabla('doc_ctr_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'dl' => false,
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc
]);
$oHash->setCamposNo('dl');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_de_ctr.phtml', $a_campos);

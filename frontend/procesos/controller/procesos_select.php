<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use web\Desplegable;
use web\Hash;
use web\Posicion;

require_once("frontend/shared/global_header_front.inc");

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_select_data', []);
$aTiposProceso = $data['a_tipos_proceso'] ?? [];

$oDespl = new Desplegable();
$oDespl->setOpciones($aTiposProceso);
$oDespl->setBlanco(true);

// Endpoints por accion (slice 10: split de procesos_ajax). url_ver apunta
// al frontend controller migrado en el slice 2.
$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_regenerar = $webBase . '/src/procesos/procesos_regenerar';
$url_clonar = $webBase . '/src/procesos/procesos_clonar';
$url_get = $webBase . '/src/procesos/procesos_get';
$url_get_listado = $webBase . '/src/procesos/procesos_get_listado';
$url_update = $webBase . '/src/procesos/procesos_update';
$url_eliminar = $webBase . '/src/procesos/procesos_eliminar';
$url_ver = 'frontend/procesos/controller/procesos_ver.php';

$oHashRegenerar = new Hash();
$oHashRegenerar->setUrl($url_regenerar);
$oHashRegenerar->setCamposForm('id_tipo_proceso');
$h_regenerar = $oHashRegenerar->linkSinVal();

$oHashGet = new Hash();
$oHashGet->setUrl($url_get);
$oHashGet->setCamposForm('id_tipo_proceso');
$h_get = $oHashGet->linkSinVal();

$oHashGetListado = new Hash();
$oHashGetListado->setUrl($url_get_listado);
$oHashGetListado->setCamposForm('id_tipo_proceso');
$h_get_listado = $oHashGetListado->linkSinVal();

$oHashClone = new Hash();
$oHashClone->setUrl($url_clonar);
$oHashClone->setCamposForm('id_tipo_proceso!id_tipo_proceso_ref');
$h_clonar = $oHashClone->linkSinVal();

$oHashDel = new Hash();
$oHashDel->setUrl($url_eliminar);
$oHashDel->setCamposForm('id_item');
$h_eliminar = $oHashDel->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ver);
$oHashNew->setCamposForm('mod!id_tipo_proceso');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new Hash();
$oHashMod->setUrl($url_ver);
$oHashMod->setCamposForm('mod!id_item!id_tipo_proceso');
$h_modificar = $oHashMod->linkSinVal();

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");
$txt_clonar = _("No ha determinado para que proceso");

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_regenerar' => $h_regenerar,
    'h_get' => $h_get,
    'h_get_listado' => $h_get_listado,
    'h_clonar' => $h_clonar,
    'h_eliminar' => $h_eliminar,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'oDespl' => $oDespl,
    'url_regenerar' => $url_regenerar,
    'url_clonar' => $url_clonar,
    'url_get' => $url_get,
    'url_get_listado' => $url_get_listado,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'url_ver' => $url_ver,
    'txt_eliminar' => $txt_eliminar,
    'txt_clonar' => $txt_clonar,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('procesos_select.html.twig', $a_campos);

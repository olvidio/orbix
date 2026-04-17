<?php

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

// Hasta que caigan los slices 2 y siguientes, el JS apunta a los controladores
// legacy de apps/ para no romper la edicion / ajax.
$url_ajax = "apps/procesos/controller/procesos_ajax.php";
$url_ver = "apps/procesos/controller/procesos_ver.php";

$oHashAct = new Hash();
$oHashAct->setUrl($url_ajax);
$oHashAct->setCamposForm('que!id_tipo_proceso');
$h_actualizar = $oHashAct->linkSinVal();

$oHashClone = new Hash();
$oHashClone->setUrl($url_ajax);
$oHashClone->setCamposForm('que!id_tipo_proceso!id_tipo_proceso_ref');
$h_clonar = $oHashClone->linkSinVal();

$oHashDel = new Hash();
$oHashDel->setUrl($url_ajax);
$oHashDel->setCamposForm('que!id_item');
$h_eliminar = $oHashDel->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ver);
$oHashNew->setCamposForm('mod!id_tipo_proceso');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new Hash();
$oHashMod->setUrl($url_ver);
$oHashMod->setCamposForm('mod!id_item!id_tipo_proceso');
$h_modificar = $oHashMod->linkSinVal();

$oHashMover = new Hash();
$oHashMover->setUrl($url_ajax);
$oHashMover->setCamposForm('que!id_item!orden');
$h_mover = $oHashMover->linkSinVal();

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");
$txt_clonar = _("No ha determinado para que proceso");

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_actualizar' => $h_actualizar,
    'h_clonar' => $h_clonar,
    'h_eliminar' => $h_eliminar,
    'h_nuevo' => $h_nuevo,
    'h_modificar' => $h_modificar,
    'h_mover' => $h_mover,
    'oDespl' => $oDespl,
    'url_ajax' => $url_ajax,
    'url_ver' => $url_ver,
    'txt_eliminar' => $txt_eliminar,
    'txt_clonar' => $txt_clonar,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('procesos_select.html.twig', $a_campos);

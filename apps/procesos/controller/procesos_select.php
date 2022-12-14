<?php

use core\ConfigGlobal;
use procesos\model\entity\GestorProcesoTipo;
use web\Hash;

/**
 * Esta página muestra el cuadro para seleccionar el proceso
 *
 *
 * @package    delegacion
 * @subpackage    procesos
 * @author    Daniel Serrabou
 * @since        07/12/18.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$oLista = new GestorProcesoTipo();
$oDespl = $oLista->getListaProcesoTipos();

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

$oHashDel = new web\Hash();
$oHashDel->setUrl($url_ajax);
$oHashDel->setCamposForm('que!id_item');
$h_eliminar = $oHashDel->linkSinVal();

$oHashNew = new web\Hash();
$oHashNew->setUrl($url_ver);
$oHashNew->setCamposForm('mod!id_tipo_proceso');
$h_nuevo = $oHashNew->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl($url_ver);
$oHashMod->setCamposForm('mod!id_item!id_tipo_proceso');
$h_modificar = $oHashMod->linkSinVal();

$oHashMover = new web\Hash();
$oHashMover->setUrl($url_ajax);
$oHashMover->setCamposForm('que!id_item!orden');
$h_mover = $oHashMover->linkSinVal();

$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");
$txt_clonar = _("No ha determinado para que proceso");

$pag_flowChart = ConfigGlobal::getWeb_scripts() . '/dani_workflowChart.js';

$a_campos = ['oPosicion' => $oPosicion,
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
    'pag_flowChart' => $pag_flowChart,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('procesos_select.html.twig', $a_campos);

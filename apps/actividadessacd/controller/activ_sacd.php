<?php

use web\Hash;
use web\PeriodoQue;

/**
 * Esta página lista las actividades con los sacd encargados.
 * Permite cambiar el orden de los sacd, eliminar y añadir.
 * Todas las acciones las ejecuta: activ_sacd_ajax.php
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Daniel Serrabou
 * @since        15/6/09.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qrefresh = (integer)\filter_input(INPUT_POST, 'refresh');

$Qtipo = (string)\filter_input(INPUT_POST, 'tipo');


$Qyear = (string)\filter_input(INPUT_POST, 'year');
$Qperiodo = (string)\filter_input(INPUT_POST, 'periodo');

$titulo = core\strtoupper_dlb(_("periodo del listado del año próximo"));
$titulo .= '. ';
$titulo .= '(' . sprintf(_("actividades de %s"), $Qtipo) . ')';
$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setTitulo($titulo);
$oFormP->setFormName('frm_cond');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setBoton("<input type=button name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_ver();\">");

$url_ajax = "apps/actividadessacd/controller/activ_sacd_ajax.php";

$oHashAsig = new Hash();
$oHashAsig->setUrl($url_ajax);
$oHashAsig->setcamposForm('que!id_activ!id_nom');
$h_asignar = $oHashAsig->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setcamposForm('que!seleccion!id_activ!f_ini_act!f_fin_act');
$h_nuevo = $oHashNew->linkSinVal();

$oHashOrden = new Hash();
$oHashOrden->setUrl($url_ajax);
$oHashOrden->setcamposForm('que!id_activ!id_nom!id_cargo!num_orden');
$h_orden = $oHashOrden->linkSinVal();

$oHashAct = new Hash();
$oHashAct->setUrl($url_ajax);
$oHashAct->setcamposForm('que!id_activ');
$h_actualizar = $oHashAct->linkSinVal();

$oHash = new Hash();
$oHash->setCamposForm('empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$a_camposHidden = array(
    'que' => 'lista_activ',
    'tipo' => $Qtipo,
);
$oHash->setArraycamposHidden($a_camposHidden);

$perm_des = FALSE;
if ($_SESSION['oPerm']->have_perm_oficina('des')) {
    $perm_des = TRUE;
}

$a_campos = ['oPosicion' => $oPosicion,
    'h_asignar' => $h_asignar,
    'h_actualizar' => $h_actualizar,
    'h_nuevo' => $h_nuevo,
    'h_orden' => $h_orden,
    'oHash' => $oHash,
    'tipo' => $Qtipo,
    'oFormP' => $oFormP,
    'url_ajax' => $url_ajax,
    'perm_des' => $perm_des,
];

$oView = new core\ViewTwig('actividadessacd/controller');
echo $oView->render('activ_sacd.html.twig', $a_campos);

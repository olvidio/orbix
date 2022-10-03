<?php

use web\Hash;
use web\PeriodoQue;
use core\ConfigGlobal;

/**
 * Esta página lista las actividades de s y sg con los centros encargados.
 * Permite cambiar el orden de los centros, eliminar y añadir.
 *
 * @package    delegacion
 * @subpackage actividades
 * @author    Daniel Serrabou
 * @since        15/3/09.
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
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setBoton("<input type=button name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_ver();\">");


$url_ajax = "apps/actividadescentro/controller/activ_ctr_ajax.php";

$oHashAsig = new Hash();
$oHashAsig->setUrl($url_ajax);
$oHashAsig->setcamposForm('que!id_activ!id_ubi');
$h_asignar = $oHashAsig->linkSinVal();

$oHashAct = new Hash();
$oHashAct->setUrl($url_ajax);
$oHashAct->setcamposForm('que!id_activ');
$h_actualizar = $oHashAct->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setcamposForm('que!id_activ!inicio!fin!f_ini_act!f_fin_act');
$h_nuevo = $oHashNew->linkSinVal();

$oHashOrden = new Hash();
$oHashOrden->setUrl($url_ajax);
$oHashOrden->setcamposForm('que!id_activ!id_ubi!num_orden');
$h_orden = $oHashOrden->linkSinVal();

$oHash = new Hash();
$oHash->setCamposForm('empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');

// $Qtipo viene por menú. Para la sf, debería ser sfsg pero en el menu está sólo sg:
if (ConfigGlobal::mi_sfsv() == 2) {
    switch ($Qtipo) {
        case 'sg':
            $Qtipo = 'sfsg';
            break;
        case 'sr':
            $Qtipo = 'sfsr';
            break;
        case 'nagd':
            $Qtipo = 'sfnagd';
            break;
    }
}

$a_camposHidden = array(
    'que' => 'lista_activ',
    'tipo' => $Qtipo,
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'h_asignar' => $h_asignar,
    'h_actualizar' => $h_actualizar,
    'h_nuevo' => $h_nuevo,
    'h_orden' => $h_orden,
    'oHash' => $oHash,
    'tipo' => $Qtipo,
    'oFormP' => $oFormP,
    'url_ajax' => $url_ajax,
];

$oView = new core\ViewTwig('actividadescentro/controller');
echo $oView->render('activ_ctr.html.twig', $a_campos);
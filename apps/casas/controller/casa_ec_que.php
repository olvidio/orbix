<?php
/**
 * Esta página sirve para seleccionar una casa, y posteriormente mostrar una lista
 * de los presupuestos de esta casa para sf,sv i total.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        22/12/2010
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use function core\strtoupper_dlb;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\CasasQue;
use web\DesplegableArray;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo_lista = (string)filter_input(INPUT_POST, 'tipo_lista');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');


$oForm = new CasasQue();
// miro que rol tengo. Si soy casa, sólo veo la mía
$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miRolePau = ConfigGlobal::mi_role_pau();
if ($miRolePau == Role::PAU_CDC) { //casa
    $id_pau = $oMiUsuario->getId_pau();
    $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
    //formulario para casas cuyo calendario de actividades interesa
    $donde = "WHERE status='t' AND (id_ubi=$sDonde)";
    $oForm->setCasas('casa');
} else {
    // Sólo quiero ver las casas comunes.
    //$donde="WHERE status='t' AND sf='t' AND sv='t'";
    // o (ara) no:
    if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
        $oForm->setCasas('all');
        $donde = "WHERE status='t'";
    } else {
        if (ConfigGlobal::mi_sfsv() == 1) {
            $oForm->setCasas('sv');
            $donde = "WHERE status='t' AND sv='t'";
        } elseif (ConfigGlobal::mi_sfsv() == 2) {
            $oForm->setCasas('sf');
            $donde = "WHERE status='t' AND sf='t'";
        }
    }
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('');
// para seleccionar más de una casa
$aOpcionesCasas = $oForm->getPosiblesCasas();
$oSelects = new DesplegableArray('', $aOpcionesCasas, 'id_cdc');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_casas(event)');

$oForm->setTitulo(strtoupper_dlb(_("resumen económico")));
$oForm->setBoton("<input type=button name=\"buscar\" value=\"" . _('buscar') . "\" onclick=\"fnjs_ver();\">");


$url_ajax = 'apps/casas/controller/casa_ajax.php';
$url_resumen = 'apps/casas/controller/casas_resumen_ajax.php';

$oHash = new web\Hash();
$sCamposForm = 'cdc_sel!id_cdc!id_cdc_mas!id_cdc_num!que';
$oHash->setcamposForm($sCamposForm);

$oHashEdit = new web\Hash();
$oHashEdit->setUrl($url_ajax);
$oHashEdit->setcamposForm('que!id_activ');
$h_edit = $oHashEdit->linkSinVal();

$param = '';
$param = "cdc_sel=9&que=get";

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'url_resumen' => $url_resumen,
    'h_edit' => $h_edit,
    'url_resumen' => $url_resumen,
    'param' => $param,
    'oForm' => $oForm,
    'oSelects' => $oSelects,
    'periodo' => $Qperiodo,
];

$oView = new core\ViewTwig('casas/controller');
echo $oView->render('casa_ec_que.html.twig', $a_campos);

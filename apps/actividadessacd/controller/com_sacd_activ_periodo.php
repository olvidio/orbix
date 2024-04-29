<?php

use function core\strtoupper_dlb;
use web\Hash;
use web\PeriodoQue;
use core\ConfigGlobal;
use usuarios\model\entity\Usuario;

/**
 * Esta página muestra un formulario con las opciones para escoger el periodo.
 *
 * @package    delegacion
 * @subpackage    des,actividades
 * @author    Daniel Serrabou
 * @since        17/4/07.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');

// para el caso de un solo sacd
$Qque = (string)filter_input(INPUT_POST, 'que');
if ($Qque === 'un_sacd') {
     $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $Qid_tabla = strtok("#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    } else {
        if (empty($Qid_nom)) {
            $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
        }
        $Qid_tabla = (integer)filter_input(INPUT_POST, 'id_tabla');
    }
    // periodo por defecto:
    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_crt';
        $Qyear = date('Y');
    }
}

$oPosicion->recordar();

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
$oFormP->setFormName('seleccion');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);

$sBotonBuscar = "<input type=button name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_ver();\">";
$sBotonEnviar = "<input type=button name=\"enviar\" value=\"" . _("enviar mail") . "\" onclick=\"fnjs_enviar_mails();\">";
$oFormP->setBoton("$sBotonBuscar  $sBotonEnviar");

$url = "apps/actividadessacd/controller/com_sacd_activ.php";

$url_com_txt = Hash::link('apps/actividadessacd/controller/com_sacd_txt.php');

$oHash = new Hash();
$oHash->setCamposForm('empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$a_camposHidden = array(
    'sacd' => 'uno',
    'id_nom' => $Qid_nom,
    'que' => 'nagd',
    'propuesta' => $Qpropuesta,
    'mail' => '',
);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('mail');

$perm_mod_txt = TRUE;
$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario->isRole('p-sacd')) {
    $perm_mod_txt = FALSE;
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'url' => $url,
    'perm_mod_txt' => $perm_mod_txt,
    'url_com_txt' => $url_com_txt,
];

$oView = new core\ViewTwig('actividadessacd/controller');
$oView->renderizar('com_sacd_activ_periodo.html.twig', $a_campos);

<?php

use actividadessacd\model\ActividadesSacdFunciones;
use actividadessacd\model\ComunicarActividadesSacd;
use core\ConfigGlobal;
use personas\model\entity\GestorPersonaEx;
use personas\model\entity\GestorPersonaSacd;
use personas\model\entity\GestorPersonaSSSC;
use personas\model\entity\PersonaSacd;
use usuarios\model\entity\Usuario;
use web\DateTimeLocal;
use web\Hash;
use web\Periodo;

/**
 * Esta página muestra las actividades que tiene que atender un sacd.
 *
 * @package    delegacion
 * @subpackage    des
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

/* claves:
 *       'com_sacd'
 *       't_propio'
 *       't_f_ini'
 *       't_f_fin'
 *       't_nombre_ubi'
 *       't_sfsv'
 *       't_actividad'
 *       't_asistentes'
 *       't_encargado'
 *       't_observ'
 *       't_nom_tipo'
 *
 */

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$Qmail = (string)filter_input(INPUT_POST, 'mail');
$Qmail = empty($Qmail) ? 'no' : $Qmail;

$oPosicion->recordar();

$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.');
// ciudad de la dl
$oActividadesSacdFunciones = new ActividadesSacdFunciones();
$poblacion = $oActividadesSacdFunciones->getLugar_dl();
$lugar_fecha = "$poblacion, $hoy_local";

$oMiUsuario = new Usuario(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
if ($oMiUsuario->isRole('p-sacd')) {
    $Qid_nom = $oMiUsuario->getId_pau();
    $Qque = 'un_sacd';
}

// Si vengo de la página personas_select.php, sólo quiero ver la lista de un sacd.
if ($Qque === "un_sacd") {
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

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

// lista de actividades posibles en el periodo.
if (empty($inicioIso) || empty($finIso)) {
    exit ("<br>" . _("falta determinar un periodo"));
}

// los sacd
$mi_dele = ConfigGlobal::mi_delef();
if (empty($Qque)) {
    $Qque = "nagd";
}
$aWhereP = [];
switch ($Qque) {
    case "nagd":
        $aWhereP['id_tabla'] = '^n|^a';
        $aWhereP['situacion'] = 'A';
        $aWhereP['sacd'] = 't';
        $aWhereP['dl'] = $mi_dele;
        $aWhereP['_ordre'] = 'apellido1,apellido2,nom';
        $aOperadorP['id_tabla'] = '~';
        $GesPersonas = new GestorPersonaSacd();
        $cPersonas = $GesPersonas->getPersonas($aWhereP, $aOperadorP);
        break;
    case "sssc":
        $aWhereP['situacion'] = 'A';
        $aWhereP['sacd'] = 't';
        $aWhereP['dl'] = $mi_dele;
        $aWhereP['_ordre'] = 'apellido1,apellido2,nom';
        $GesPersonas = new GestorPersonaSSSC();
        $cPersonas = $GesPersonas->getPersonas($aWhereP);
        break;
    case "un_sacd":
        $oPersona = new PersonaSacd($Qid_nom);
        $cPersonas = array($oPersona);
        break;
}

$oComunicarActividadesSacd = new ComunicarActividadesSacd();
$oComunicarActividadesSacd->setInicioIso($inicioIso);
$oComunicarActividadesSacd->setFinIso($finIso);
$oComunicarActividadesSacd->setPropuesta($Qpropuesta);

$oComunicarActividadesSacd->setPersonas($cPersonas);

$array_actividades = $oComunicarActividadesSacd->getArrayComunicacion();

$a_campos = ['oPosicion' => $oPosicion,
    'array_actividades' => $array_actividades,
    'Qque' => $Qque,
    'mi_dele' => $mi_dele,
    'lugar_fecha' => $lugar_fecha,
    'propuesta' => $Qpropuesta,
];

if ($Qmail === 'no') {
    if ($Qque === 'un_sacd') {
        $periodo_txt = sprintf(_("atención actividades para el periodo %s"), $oPeriodo->getTxt_cusro());
        $url = "apps/actividadessacd/controller/com_sacd_activ.php";

        $oHash = new Hash();
        $a_camposHidden = array(
            'id_nom' => $Qid_nom,
            'que' => $Qque,
            'mail' => '',
        );
        $oHash->setArraycamposHidden($a_camposHidden);
        $oHash->setCamposNo('mail');

        $a_campos = [ 'oPosicion' => $oPosicion,
            'oHash' => $oHash,
            'url' => $url,
            'array_actividades' => $array_actividades,
            'Qque' => $Qque,
            'mi_dele' => $mi_dele,
            'lugar_fecha' => $lugar_fecha,
            'propuesta' => $Qpropuesta,
            'periodo_txt' => $periodo_txt,
        ];

        $oView = new core\View('actividadessacd/controller');
        $oView->renderizar('com_un_sacd_activ_print.phtml', $a_campos);
    } else {
        $oView = new core\View('actividadessacd/controller');
        $oView->renderizar('com_sacd_activ_print.phtml', $a_campos);
    }
}

// Añado la lista de los sacd de paso:
$array_actividades_de_paso = [];
if ($Qque !== "un_sacd" && $Qmail === 'no') {
    $aWhereP = [];
    $aWhereP['situacion'] = 'A';
    $aWhereP['sacd'] = 't';
    $aWhereP['dl'] = $mi_dele;
    $aWhereP['_ordre'] = 'apellido1,apellido2,nom';
    $GesPersonas = new GestorPersonaEx();
    $cPersonas = $GesPersonas->getPersonas($aWhereP);

    $oComunicarActividadesSacd = new ComunicarActividadesSacd();
    $oComunicarActividadesSacd->setInicioIso($inicioIso);
    $oComunicarActividadesSacd->setFinIso($finIso);
    $oComunicarActividadesSacd->setPropuesta($Qpropuesta);
    $oComunicarActividadesSacd->setSoloCargos(TRUE);
    $oComunicarActividadesSacd->setQuitarInactivos(TRUE);

    $oComunicarActividadesSacd->setPersonas($cPersonas);

    $array_actividades_de_paso = $oComunicarActividadesSacd->getArrayComunicacion();

    if (count($array_actividades) > 0) {
        $a_campos = ['oPosicion' => $oPosicion,
            'array_actividades' => $array_actividades_de_paso,
            'Qque' => $Qque,
            'mi_dele' => $mi_dele,
            'lugar_fecha' => $lugar_fecha,
            'propuesta' => $Qpropuesta,
        ];

        echo "<br><br><hr>";
        echo "<h3>" . _("Sacd de paso") . "</h3>";
        echo "<hr>";
        $oView->renderizar('com_sacd_activ_print.phtml', $a_campos);
    }
}

if ($Qmail === 'si') {
    $oComunicarActividadesSacd->envairMails($array_actividades);
}
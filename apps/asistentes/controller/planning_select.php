<?php

use personas\model\entity as personas;
use ubis\model\entity as ubis;
use web\Periodo;

/**
 * Página de selección de las personas para las que se trazará un planning
 * Presenta una lista de personas que cumplen la condición fijada en el formulario
 * podemos venir de la página plannig_que.php
 * Condiciones:
 *    por formulario:
 *        apellido1, apellido2, nombre, centro
 *        periodo, year -> se calcula $inicio y $fin que son las que se pasan.
 *    por menu:
 *        na -> 'n' o 'a' para distinguir numerarios o agregados de paso
 *        tabla-> 'p_de_paso' (de momento sólo he encontrado esta condicion)
 *
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion->goStack($stack);
        $Qtipo = $oPosicion->getParametro('tipo');
        $Qobj_pau = $oPosicion->getParametro('obj_pau');
        $Qna = $oPosicion->getParametro('na');
        $Qperiodo = $oPosicion->getParametro('periodo');
        $Qyear = $oPosicion->getParametro('year');
        $Qempiezamin = $oPosicion->getParametro('empiezamin');
        $Qempiezamax = $oPosicion->getParametro('empiezamax');
        $QsaWhere = $oPosicion->getParametro('saWhere');
        $QsaOperador = $oPosicion->getParametro('saOperador');
        $QsaWhereCtr = $oPosicion->getParametro('saWhereCtr');
        $QsaOperadorCtr = $oPosicion->getParametro('saOperadorCtr');
        $Qid_sel = $oPosicion->getParametro('id_sel');
        $Qscroll_id = $oPosicion->getParametro('scroll_id');
        $oPosicion->olvidar($stack); //limpio todos los estados hacia delante.

        $aWhere = unserialize(base64_decode($QsaWhere));
        $aOperador = unserialize(base64_decode($QsaOperador));
        $aWhereCtr = unserialize(base64_decode($QsaWhereCtr));
        $aOperadorCtr = unserialize(base64_decode($QsaOperadorCtr));
    }
} else { //si no vengo por goto.
    $Qtipo = (string)\filter_input(INPUT_POST, 'tipo');
    $Qobj_pau = (string)\filter_input(INPUT_POST, 'obj_pau');
    $Qna = (string)\filter_input(INPUT_POST, 'na');
    $Qyear = (string)\filter_input(INPUT_POST, 'year');
    $Qperiodo = (string)\filter_input(INPUT_POST, 'periodo');
    $Qempiezamin = (string)\filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)\filter_input(INPUT_POST, 'empiezamax');

    /*miro las condiciones. las variables son: num, agd, sup, nombre, apellido1, apellido2 */
    $Qapellido1 = (string)\filter_input(INPUT_POST, 'apellido1');
    $Qapellido2 = (string)\filter_input(INPUT_POST, 'apellido2');
    $Qnombre = (string)\filter_input(INPUT_POST, 'nombre');
    $Qcentro = (string)\filter_input(INPUT_POST, 'centro');
    $Qna = (string)\filter_input(INPUT_POST, 'na');

    $aWhere = array();
    $aOperador = array();
    $aWhereCtr = array();
    $aOperadorCtr = array();
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    if (!empty($Qapellido1)) {
        $aWhere['apellido1'] = "^" . $Qapellido1;
        $aOperador['apellido1'] = 'sin_acentos';
    }
    if (!empty($Qapellido2)) {
        $aWhere['apellido2'] = "^" . $Qapellido2;
        $aOperador['apellido2'] = 'sin_acentos';
    }
    if (!empty($Qnombre)) {
        $aWhere['nom'] = "^" . $Qnombre;
        $aOperador['nom'] = 'sin_acentos';
    }

    /*Si está puesto el nombre del centro, saco una lista de todos los del centro*/
    if (!empty($Qcentro)) {
        $nom_ubi = str_replace("+", "\+", $Qcentro); // para los centros de la sss+
        $aWhereCtr['nombre_ubi'] = $nom_ubi;
        $aOperadorCtr['nombre_ubi'] = 'sin_acentos';
    }
    // Estos valores vienen por el menu
    if (!empty($Qna)) {
        $aWhere['id_tabla'] = 'p' . $Qna;
    }
    $QsaWhere = base64_encode(serialize($aWhere));
    $QsaOperador = base64_encode(serialize($aOperador));
    $QsaWhereCtr = base64_encode(serialize($aWhereCtr));
    $QsaOperadorCtr = base64_encode(serialize($aOperadorCtr));
}

if (!empty($aWhereCtr)) { // si busco por centro sólo puede ser de casa
    $GesCentroDl = new ubis\GestorCentroDl();
    $cCentros = $GesCentroDl->getCentros($aWhereCtr, $aOperadorCtr);
    // por si hay más de uno.
    $cPersonas = array();
    foreach ($cCentros as $oCentro) {
        $id_ubi = $oCentro->getId_ubi();
        $aWhere['id_ctr'] = $id_ubi;
        if (!isset($aOperador)) $aOperador = array();
        $GesPersonas = new personas\GestorPersonaDl();
        $cPersonas2 = $GesPersonas->getPersonasDl($aWhere, $aOperador);
        if (is_array($cPersonas2) && count($cPersonas2) >= 1) {
            if (is_array($cPersonas)) {
                $cPersonas = $cPersonas + $cPersonas2;
            } else {
                $cPersonas = $cPersonas2;
            }
        }
    }
} else {
    switch ($Qobj_pau) {
        case 'PersonaN':
            $GesPersonas = new personas\GestorPersonaN();
            break;
        case 'PersonaAgd':
            $GesPersonas = new personas\GestorPersonaAgd();
            break;
        case 'PersonaNax':
            $GesPersonas = new personas\GestorPersonaNax();
            break;
        case 'PersonaS':
            $GesPersonas = new personas\GestorPersonaS();
            break;
        case 'PersonaSSSC':
            $GesPersonas = new personas\GestorPersonaSSSC();
            break;
        case 'PersonaDl':
            $GesPersonas = new personas\GestorPersonaDl();
            break;
        default:
            $GesPersonas = new personas\GestorPersonaDl();
    }
    $cPersonas = $GesPersonas->getPersonas($aWhere, $aOperador);
}

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'saWhere' => $QsaWhere,
    'saOperador' => $QsaOperador,
    'saWhereCtr' => $QsaWhereCtr,
    'saOperadorCtr' => $QsaOperadorCtr
);
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones = array(
    array('txt' => _("vista tabla"), 'click' => "fnjs_ver_planning(\"#seleccionados\",1)"),
    array('txt' => _("vista grid"), 'click' => "fnjs_ver_planning(\"#seleccionados\",3)"),
    array('txt' => _("vista para imprimir"), 'click' => "fnjs_planning_print(\"#seleccionados\")"),
    array('txt' => _("ver actividades"), 'click' => "fnjs_actividades(\"#seleccionados\")")
);
$a_cabeceras = array(_("tipo"),
    array('name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'),
    _("centro")
);

$i = 0;
$a_valores = array();
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}
foreach ($cPersonas as $oPersona) {
    $i++;
    $id_nom = $oPersona->getId_nom();
    $id_tabla = $oPersona->getId_tabla();
    $nom = $oPersona->getPrefApellidosNombre();
    $ctr_o_dl = $oPersona->getCentro_o_dl();
    $condicion_2 = "Where id_nom='" . $id_nom . "'";
    $condicion_2 = urlencode($condicion_2);

    $aQuery = array('id_nom' => $id_nom,
        'condicion' => $condicion_2,
        'id_tabla' => $id_tabla);
    // el hppt_build_query no pasa los valores null
    if (is_array($aQuery)) {
        array_walk($aQuery, 'core\poner_empty_on_null');
    }
    $pagina = web\Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores[$i]['sel'] = "$id_nom";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = array('ira' => $pagina, 'valor' => $nom);
    $a_valores[$i][3] = $ctr_o_dl;
}

$oHash = new web\Hash();
$oHash->setcamposNo('sel!scroll_id!modelo!que!id_dossier');
$a_camposHidden = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'pau' => 'p',
);
$oHash->setArraycamposHidden($a_camposHidden);

$oTabla = new web\Lista();
$oTabla->setId_tabla('planning_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'num_personas' => $i,
];

$oView = new core\View('asistentes/controller');
echo $oView->render('planning_select.phtml', $a_campos);
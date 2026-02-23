<?php

use core\ViewPhtml;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Lista;
use function core\urlsafe_b64encode;

/**
 * Página de selección de las personas para las que se trazará un planning
 * Presenta una lista de personas que cumplen la condición fijada en el formulario
 * podemos venir de la página planning_que.php
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
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion->goStack($stack);
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

        $aWhere = json_decode(core\urlsafe_b64decode($QsaWhere));
        $aOperador = json_decode(core\urlsafe_b64decode($QsaOperador));
        $aWhereCtr = json_decode(core\urlsafe_b64decode($QsaWhereCtr));
        $aOperadorCtr = json_decode(core\urlsafe_b64decode($QsaOperadorCtr));
    }
} else { //si no vengo por goto.
    $Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    /*miro las condiciones. las variables son: num, agd, sup, nombre, apellido1, apellido2 */
    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    $Qapellido2 = (string)filter_input(INPUT_POST, 'apellido2');
    $Qnombre = (string)filter_input(INPUT_POST, 'nombre');
    $Qcentro = (string)filter_input(INPUT_POST, 'centro');
    $Qna = (string)filter_input(INPUT_POST, 'na');

    $aWhere = [];
    $aOperador = [];
    $aWhereCtr = [];
    $aOperadorCtr = [];
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
    $QsaWhere = urlsafe_b64encode(json_encode($aWhere), JSON_THROW_ON_ERROR);
    $QsaOperador = urlsafe_b64encode(json_encode($aOperador), JSON_THROW_ON_ERROR);
    $QsaWhereCtr = urlsafe_b64encode(json_encode($aWhereCtr), JSON_THROW_ON_ERROR);
    $QsaOperadorCtr = urlsafe_b64encode(json_encode($aOperadorCtr), JSON_THROW_ON_ERROR);
}

if (!empty($aWhereCtr)) { // si busco por centro sólo puede ser de casa
    $GesCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $cCentros = $GesCentroDl->getCentros($aWhereCtr, $aOperadorCtr);
    // por si hay más de uno.
    $cPersonas = [];
    foreach ($cCentros as $oCentro) {
        $id_ubi = $oCentro->getId_ubi();
        $aWhere['id_ctr'] = $id_ubi;
        if (!isset($aOperador)) $aOperador = [];
        $GesPersonas = new GestorPersonaDl();
        $cPersonas2 = $GesPersonas->getPersonas($aWhere, $aOperador);
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
            $PersonaRepository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
            break;
        case 'PersonaAgd':
            $PersonaRepository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
            break;
        case 'PersonaNax':
            $PersonaRepository = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
            break;
        case 'PersonaS':
            $PersonaRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
            break;
        case 'PersonaSSSC':
            $PersonaRepository = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
            break;
        case 'PersonaDl':
            $PersonaRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
            break;
        case 'PersonaEx':
            $PersonaRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
            break;
        default:
            $PersonaRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    }
    $cPersonas = $PersonaRepository->getPersonas($aWhere, $aOperador);
}

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
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
$a_valores = [];
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

    $aQuery = [
        'id_nom' => $id_nom,
        'condicion' => $condicion_2,
        'obj_pau' => $Qobj_pau,
        'id_tabla' => $id_tabla,
    ];
    // el hppt_build_query no pasa los valores null
    if (is_array($aQuery)) {
        array_walk($aQuery, 'core\poner_empty_on_null');
    }
    $pagina = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores[$i]['sel'] = "$id_nom";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = array('ira' => $pagina, 'valor' => $nom);
    $a_valores[$i][3] = $ctr_o_dl;
}

$oHash = new Hash();
$oHash->setcamposNo('sel!scroll_id!modelo!que!id_dossier');
$a_camposHidden = array(
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'pau' => 'p',
);
$oHash->setArraycamposHidden($a_camposHidden);

$oTabla = new Lista();
$oTabla->setId_tabla('planning_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'num_personas' => $i,
];

$oView = new ViewPhtml('planning\controller');
$oView->renderizar('planning_persona_select.phtml', $a_campos);
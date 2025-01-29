<?php
/**
 * Muestra un formulario para introducir/cambiar los datos del Cargo en una
 * actividad de una persona.
 *
 *
 * @param string $_POST ['pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_pau']  para el controlador dossiers_ver
 * @param string $_POST ['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST ['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param string $_POST ['mod_curso']  para mantener la selección del curso
 * @param integer $_POST ['permiso'] valores 1, 2, 3
 * @param integer $_POST ['scroll_id']
 * @param array $_POST ['sel'] con id_item#eliminar si eliminar == 2, elimina también la asistencia
 * En el caso de nuevo:
 * @param string $_POST ['que_dl'] la propia dl o vacio para otras
 * @param integer $_POST ['id_tipo'] selección del tipo de actividad
 *
 * @package    orbix
 * @subpackage    actividadcargos
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 */

//Comentario para comprobar subidas desde Eclipse (2)

use actividadcargos\model\entity\ActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\Actividad;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_item = '';
$id_cargo = '';

$Qpermiso = (integer)filter_input(INPUT_POST, 'permiso');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    $eliminar = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qque_dl = (string)filter_input(INPUT_POST, 'que_dl');
    $Qid_tipo = (integer)filter_input(INPUT_POST, 'id_tipo');
}
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$pau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
//$obj_pau = (string)  filter_input(INPUT_POST, 'obj_pau');

$obj = 'actividadcargos\\model\\entity\\ActividadCargo';

$id_activ_real = '';
$nom_activ = '';
$cActividades = array();
if (!empty($Qid_item)) { //caso de modificar
    $oActividadCargo = new ActividadCargo(array('id_item' => $Qid_item));
    $id_activ = $oActividadCargo->getId_activ();
    $id_nom = $oActividadCargo->getId_nom();
    $id_cargo = $oActividadCargo->getId_cargo();
    $puede_agd = $oActividadCargo->getPuede_agd();
    $observ = $oActividadCargo->getObserv();

    $oActividad = new Actividad(array('id_activ' => $id_activ));
    $nom_activ = $oActividad->getNom_activ();
    // si es de la sf quito la 'f'
    $dl = preg_replace('/f$/', '', $oActividad->getDl_org());
    $id_tabla_dl = $oActividad->getId_tabla();
    $id_activ_real = $id_activ;
} else { //caso de nuevo cargo
    if (empty($Qid_tipo)) {
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $id_tipo = '^' . $mi_sfsv;  //caso genérico para todas las actividades
    } else {
        $id_tipo = empty($Qid_tipo) ? "" : '^' . $Qid_tipo;
    }
    if (!empty($Qque_dl)) {
        $aWhere['dl_org'] = $Qque_dl;
    } else {
        $aWhere['dl_org'] = ConfigGlobal::mi_delef();
        $aOperadores['dl_org'] = '!=';
    }

    $aWhere['id_tipo_activ'] = $id_tipo;
    $aOperadores['id_tipo_activ'] = '~';
    $aWhere['status'] = ActividadAll::STATUS_ACTUAL;
    $aWhere['_ordre'] = 'f_ini';

    $oGesActividades = new GestorActividad();
    $cActividades = $oGesActividades->getActividades($aWhere, $aOperadores);

    $puede_agd = "f"; //valor por defecto
    $observ = ""; //valor por defecto
}

$oCargos = new GestorCargo();
$oDesplegableCargos = $oCargos->getListaCargos();
$oDesplegableCargos->setNombre('id_cargo');
$oDesplegableCargos->setBlanco(false);
$oDesplegableCargos->setOpcion_sel($id_cargo);
$chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';

$oHash = new Hash();
$camposForm = 'id_cargo!observ';
$camposNo = 'puede_agd';
$a_camposHidden = array(
    'id_item' => $Qid_item,
    'id_nom' => $Qid_pau,
    'mod' => $Qmod,
);
if (!empty($id_activ_real)) {
    $a_camposHidden['id_activ'] = $id_activ_real;
} else {
    if ($Qmod === "nuevo") {
        $camposNo .= '!asis';
    }
    $camposForm .= '!id_activ';
}
$oHash->setCamposNo($camposNo);
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_activ_real' => $id_activ_real,
    'nom_activ' => $nom_activ,
    'cActividades' => $cActividades,
    'oDesplegableCargos' => $oDesplegableCargos,
    'chk' => $chk,
    'observ' => $observ,
    'Qmod' => $Qmod,
];

$oView = new ViewPhtml('actividadcargos/model');
$oView->renderizar('form_1302.phtml', $a_campos);
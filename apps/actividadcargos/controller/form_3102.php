<?php
/**
 * Muestra un formulario para introducir/cambiar los datos del Cargo en una
 * persona en una actividad.
 *
 *
 * @param string $_POST ['pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_pau']  para el controlador dossiers_ver
 * @param string $_POST ['obj_pau']  para el controlador dossiers_ver
 * @param integer $_POST ['id_dossier']  para el controlador dossiers_ver
 * @param string $_POST ['mod']  para el controlador dossiers_ver
 * En el caso de modificar:
 * @param integer $_POST ['permiso'] valores 1, 2, 3
 * @param integer $_POST ['scroll_id']
 * @param array $_POST ['sel'] con id_item#eliminar si eliminar == 2, elimina también la asistencia
 * En el caso de nuevo:
 *
 * @package    orbix
 * @subpackage    actividadcargos
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 *
 */

use core\ViewPhtml;
use frontend\shared\web\Desplegable;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\entity\Persona;
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
$Qid_cargo = '';

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qid_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qid_dossier === 3101) {  // vengo del listado de asistencias
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $Qid_item = (integer)strtok("#"); // si no hay devuelve false
        $Qid_item = empty($Qid_item) ? '' : $Qid_item; // cambiar el false a ''.
        $eliminar = (integer)strtok("#");
        $Qid_schema = (integer)strtok("#");
    } else {
        $Qid_item = (integer)strtok($a_sel[0], "#");
        $eliminar = (integer)strtok("#");
        $Qid_schema = (integer)strtok("#");
    }
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $Qid_schema = '';
}
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$pau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$obj = 'ActividadCargo';

$id_nom_real = '';
$ape_nom = '';
$oDesplegablePersonas = [];
if (!empty($Qid_item)) { //caso de modificar
    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    // necesario mirar el esquema en el caso de consultar las vistas de union para regiones stgr
    $cActividadCargo = $ActividadCargoRepository->getActividadCargos(['id_item' => $Qid_item, 'id_schema' => $Qid_schema]);
    $oActividadCargo = $cActividadCargo[0];
    $Qid_activ = $oActividadCargo->getId_activ();
    $Qid_cargo = $oActividadCargo->getId_cargo();
    $Qid_nom = $oActividadCargo->getId_nom();
    $puede_agd = $oActividadCargo->isPuede_agd();
    $observ = $oActividadCargo->getObserv();

    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if (!is_object($oPersona)) {
        $msg_err = "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
        exit ($msg_err);
    }
    $ape_nom = $oPersona->getPrefApellidosNombre();
    $id_tabla = $oPersona->getId_tabla();
    $id_nom_real = $Qid_nom;
} else { //caso de nuevo cargo
    $observ = "";
    // Si vengo de la lista de asistentes, ya sé el id_nom y el id_activ (es como modificar)
    if ($Qid_dossier == 3101) {  // vengo del listado de asistencias
        $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
        if (!is_object($oPersona)) {
            $msg_err = "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
            exit ($msg_err);
        }
        $ape_nom = $oPersona->getPrefApellidosNombre();
        $id_tabla = $oPersona->getId_tabla();
        $id_nom_real = $Qid_nom;
    } elseif (!empty($Qobj_pau)) {
        $obj_pau = strtok(urldecode($Qobj_pau), '&');
        $na = strtok('&');
        $na_txt = strtok($na, '=');
        $na_val = 'p' . strtok('=');
        $oDesplegablePersonas = new Desplegable();
        $oDesplegablePersonas->setNombre('id_nom');
        $oDesplegablePersonas->setBlanco(true);
        switch ($obj_pau) {
            case 'PersonaN':
                $PersonaNRepository = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
                $oOpciones = $PersonaNRepository->getArrayPersonas();
                $oDesplegablePersonas->setOpciones($oOpciones);
                break;
            case 'PersonaNax':
                $PersonaNaxRepository = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
                $oOpciones = $PersonaNaxRepository->getArrayPersonas();
                $oDesplegablePersonas->setOpciones($oOpciones);
                break;
            case 'PersonaAgd':
                $PersonaAgdRepository = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
                $oOpciones = $PersonaAgdRepository->getArrayPersonas();
                $oDesplegablePersonas->setOpciones($oOpciones);
                break;
            case 'PersonaS':
                $PersonaSRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
                $oOpciones = $PersonaSRepository->getArrayPersonas();
                $oDesplegablePersonas->setOpciones($oOpciones);
                break;
            case 'PersonaSSSC':
            case 'PersonaEx':
                $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
                $oOpciones = $PersonaExRepository->getArrayPersonas($na_val);
                $oDesplegablePersonas->setOpciones($oOpciones);
                $obj_pau = 'PersonaEx';
                break;
        }
    } else {
        echo $oPosicion->go_atras(1);
    }
}
$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$aOpciones = $CargoRepository->getArrayCargos();
$oDesplegableCargos = new Desplegable();
$oDesplegableCargos->setNombre('id_cargo');
$oDesplegableCargos->setBlanco(false);
$oDesplegableCargos->setOpciones($aOpciones);
$oDesplegableCargos->setOpcion_sel($Qid_cargo);
$chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';


$oHash = new Hash();
$camposForm = 'id_cargo!observ';
$camposNo = 'puede_agd';
$a_camposHidden = array(
    'id_item' => $Qid_item,
    'id_activ' => $Qid_pau,
    'mod' => $Qmod,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
);
if (!empty($id_nom_real)) {
    $a_camposHidden['id_nom'] = $id_nom_real;
} else {
    if ($Qmod === "nuevo") {
        $camposNo .= '!asis';
    }
    $camposForm .= '!id_nom';
}
$oHash->setCamposNo($camposNo);
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_nom_real' => $id_nom_real,
    'ape_nom' => $ape_nom,
    'oDesplegablePersonas' => $oDesplegablePersonas,
    'oDesplegableCargos' => $oDesplegableCargos,
    'chk' => $chk,
    'observ' => $observ,
    'Qmod' => $Qmod,
];

$oView = new ViewPhtml('actividadcargos\model');
$oView->renderizar('form_3102.phtml', $a_campos);
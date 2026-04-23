<?php

/**
 * Form de alta / edicion de un `ActividadCargo` desde el dossier
 * `cargos_de_actividad` (id_tipo_dossier 3102, vista centrada en la actividad).
 *
 * Tambien se invoca desde el listado de asistentes (dossier 3101) cuando hay
 * que asignar un cargo a una persona ya presente: en ese caso `id_dossier`
 * sigue llegando como 3101 y el form conoce a la persona.
 *
 * @param string  $_POST['pau']         persona/actividad abstracto (`p`/`a`)
 * @param integer $_POST['id_pau']      id de la entidad pau
 * @param string  $_POST['obj_pau']     clase pau
 * @param integer $_POST['id_dossier']  3102 | 3101
 * @param string  $_POST['mod']         `nuevo` | `editar`
 * @param integer $_POST['permiso']     1, 2, 3
 * @param array   $_POST['sel']         [id_nom#id_item#elim_asis#id_schema]
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\Persona;
use web\Hash;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qid_item = '';
$Qid_cargo = '';

$Qpermiso = (string) filter_input(INPUT_POST, 'permiso');
$Qid_dossier = (int) filter_input(INPUT_POST, 'id_dossier');
if ($Qid_dossier <= 0) {
    $Qid_dossier = 3102;
}

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_nom = (int) strtok($a_sel[0], '#');
    $Qid_item = (int) strtok('#');
    $Qid_item = empty($Qid_item) ? '' : $Qid_item;
    // posicion 3: elim_asis (para eliminar, no relevante al editar) / posicion 4: id_schema
    strtok('#');
    $Qid_schema = (int) strtok('#');
} else {
    $Qid_nom = (int) filter_input(INPUT_POST, 'id_nom');
    $Qid_schema = '';
}

$Qmod = (string) filter_input(INPUT_POST, 'mod');
$pau = (string) filter_input(INPUT_POST, 'pau');
$Qid_pau = (int) filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string) filter_input(INPUT_POST, 'obj_pau');

$obj = 'ActividadCargo';

$id_nom_real = '';
$ape_nom = '';
$observ = '';
$puede_agd = '';
$oDesplegablePersonas = [];

if (!empty($Qid_item)) {
    // editar: cargamos el ActividadCargo
    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    $cActividadCargo = $ActividadCargoRepository->getActividadCargos([
        'id_item' => $Qid_item,
        'id_schema' => $Qid_schema,
    ]);
    $oActividadCargo = $cActividadCargo[0];
    $Qid_cargo = $oActividadCargo->getId_cargo();
    $Qid_nom = $oActividadCargo->getId_nom();
    $puede_agd = $oActividadCargo->isPuede_agd();
    $observ = $oActividadCargo->getObserv();

    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if (!is_object($oPersona)) {
        exit('<br>No encuentro a nadie con id_nom: ' . $Qid_nom . ' en ' . __FILE__ . ':' . __LINE__);
    }
    $ape_nom = $oPersona->getPrefApellidosNombre();
    $id_nom_real = $Qid_nom;
} else {
    if ($Qid_dossier === 3101) {
        // Vengo desde el listado de asistentes con la persona ya fijada.
        $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
        if (!is_object($oPersona)) {
            exit('<br>No encuentro a nadie con id_nom: ' . $Qid_nom . ' en ' . __FILE__ . ':' . __LINE__);
        }
        $ape_nom = $oPersona->getPrefApellidosNombre();
        $id_nom_real = $Qid_nom;
    } elseif (!empty($Qobj_pau)) {
        $obj_pau = strtok(urldecode($Qobj_pau), '&');
        strtok('&'); // na chunk
        $oDesplegablePersonas = new Desplegable();
        $oDesplegablePersonas->setNombre('id_nom');
        $oDesplegablePersonas->setBlanco(true);
        switch ($obj_pau) {
            case 'PersonaN':
                $oOpciones = $GLOBALS['container']->get(PersonaNRepositoryInterface::class)->getArrayPersonas();
                break;
            case 'PersonaNax':
                $oOpciones = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class)->getArrayPersonas();
                break;
            case 'PersonaAgd':
                $oOpciones = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class)->getArrayPersonas();
                break;
            case 'PersonaS':
                $oOpciones = $GLOBALS['container']->get(PersonaSRepositoryInterface::class)->getArrayPersonas();
                break;
            case 'PersonaSSSC':
                $oOpciones = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class)->getArrayPersonas();
                break;
            case 'PersonaEx':
                // PersonaEx usa el filtro 'p'+na del segundo token del obj_pau.
                $rest = strtok(urldecode($Qobj_pau), '&');
                $tail = strtok('&');
                $na_val = 'p' . substr((string) $tail, (int) strpos((string) $tail, '=') + 1);
                $oOpciones = $GLOBALS['container']->get(PersonaExRepositoryInterface::class)->getArrayPersonas($na_val);
                break;
            default:
                $oOpciones = [];
        }
        $oDesplegablePersonas->setOpciones($oOpciones);
    } else {
        echo $oPosicion->go_atras(1);
        return;
    }
}

$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$oDesplegableCargos = new Desplegable();
$oDesplegableCargos->setNombre('id_cargo');
$oDesplegableCargos->setBlanco(true);
$oDesplegableCargos->setOpciones($CargoRepository->getArrayCargos());
$oDesplegableCargos->setOpcion_sel($Qid_cargo);

$chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';

$oHash = new Hash();
$camposForm = 'id_cargo!observ';
$camposNo = 'puede_agd';
$a_camposHidden = [
    'id_item' => $Qid_item,
    'id_activ' => $Qid_pau,
    'mod' => $Qmod,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
];
if (!empty($id_nom_real)) {
    $a_camposHidden['id_nom'] = $id_nom_real;
} else {
    if ($Qmod === 'nuevo') {
        $camposNo .= '!asis';
        $camposForm .= '!asis_presente';
    }
    $camposForm .= '!id_nom';
}
$oHash->setCamposNo($camposNo);
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_cargo_nuevo = $web . '/src/actividadcargos/cargo_nuevo';
$url_cargo_editar = $web . '/src/actividadcargos/cargo_editar';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_nom_real' => $id_nom_real,
    'ape_nom' => $ape_nom,
    'oDesplegablePersonas' => $oDesplegablePersonas,
    'oDesplegableCargos' => $oDesplegableCargos,
    'chk' => $chk,
    'observ' => $observ,
    'Qmod' => $Qmod,
    'url_cargo_nuevo' => $url_cargo_nuevo,
    'url_cargo_editar' => $url_cargo_editar,
];

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_de_actividad.phtml', $a_campos);

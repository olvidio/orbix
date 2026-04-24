<?php

/**
 * Form de alta / edicion de un `ActividadCargo` desde el dossier
 * `cargos_personas_en_actividad` (id_tipo_dossier 1302, vista centrada en la
 * persona: listamos las actividades en las que tiene cargo).
 *
 * @param string  $_POST['pau']
 * @param integer $_POST['id_pau']      id_nom de la persona
 * @param string  $_POST['obj_pau']
 * @param integer $_POST['id_dossier']  1302
 * @param string  $_POST['mod']         `nuevo` | `editar`
 * @param string  $_POST['que_dl']      dl propia / '' = otras
 * @param integer $_POST['id_tipo']     tipo de actividad para el listado
 * @param integer $_POST['permiso']     1, 2, 3
 * @param array   $_POST['sel']         [id_item#elim_asis]
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use web\Hash;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qid_item = '';
$id_cargo = '';

$Qpermiso = (int) filter_input(INPUT_POST, 'permiso');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qque_dl = '';
$Qid_tipo = 0;
if (!empty($a_sel)) {
    $Qid_item = (int) strtok($a_sel[0], '#');
} else {
    $Qque_dl = (string) filter_input(INPUT_POST, 'que_dl');
    $Qid_tipo = (int) filter_input(INPUT_POST, 'id_tipo');
}

$Qmod = (string) filter_input(INPUT_POST, 'mod');
$pau = (string) filter_input(INPUT_POST, 'pau');
$Qid_pau = (int) filter_input(INPUT_POST, 'id_pau');
$Qid_dossier = (int) filter_input(INPUT_POST, 'id_dossier');
if ($Qid_dossier <= 0) {
    $Qid_dossier = 1302;
}

$obj = 'actividadcargos\\model\\entity\\ActividadCargo';

$id_activ_real = '';
$nom_activ = '';
$observ = '';
$puede_agd = '';
$cActividades = [];

if (!empty($Qid_item)) {
    $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
    $oActividadCargo = $ActividadCargoRepository->findById($Qid_item);
    $id_activ = $oActividadCargo->getId_activ();
    $id_cargo = $oActividadCargo->getId_cargo();
    $puede_agd = $oActividadCargo->isPuede_agd();
    $observ = $oActividadCargo->getObserv();

    $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
    $oActividad = $ActividadRepository->findById($id_activ);
    $nom_activ = $oActividad->getNom_activ();
    $id_activ_real = $id_activ;
} else {
    if (empty($Qid_tipo)) {
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $id_tipo = '^' . $mi_sfsv;
    } else {
        $id_tipo = '^' . $Qid_tipo;
    }
    $aWhere = [];
    $aOperadores = [];
    if (!empty($Qque_dl)) {
        $aWhere['dl_org'] = $Qque_dl;
    } else {
        $aWhere['dl_org'] = ConfigGlobal::mi_delef();
        $aOperadores['dl_org'] = '!=';
    }
    $aWhere['id_tipo_activ'] = $id_tipo;
    $aOperadores['id_tipo_activ'] = '~';
    $aWhere['status'] = StatusId::ACTUAL;
    $aWhere['_ordre'] = 'f_ini';

    $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
    $cActividades = $ActividadRepository->getActividades($aWhere, $aOperadores);
}

$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$oDesplegableCargos = new Desplegable();
$oDesplegableCargos->setNombre('id_cargo');
$oDesplegableCargos->setBlanco(false);
$oDesplegableCargos->setOpciones($CargoRepository->getArrayCargos());
$oDesplegableCargos->setOpcion_sel($id_cargo);

$chk = (!empty($puede_agd) && is_true($puede_agd)) ? 'checked' : '';

$oHash = new Hash();
$camposForm = 'id_cargo!observ';
$camposNo = 'puede_agd';
$a_camposHidden = [
    'id_item' => $Qid_item,
    'id_nom' => $Qid_pau,
    'mod' => $Qmod,
];
if (!empty($id_activ_real)) {
    $a_camposHidden['id_activ'] = $id_activ_real;
} else {
    if ($Qmod === 'nuevo') {
        $camposNo .= '!asis';
        $camposForm .= '!asis_presente';
    }
    $camposForm .= '!id_activ';
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
    'id_activ_real' => $id_activ_real,
    'nom_activ' => $nom_activ,
    'cActividades' => $cActividades,
    'oDesplegableCargos' => $oDesplegableCargos,
    'chk' => $chk,
    'observ' => $observ,
    'Qmod' => $Qmod,
    'url_cargo_nuevo' => $url_cargo_nuevo,
    'url_cargo_editar' => $url_cargo_editar,
];

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_personas_en_actividad.phtml', $a_campos);

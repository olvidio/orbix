<?php

namespace frontend\personas\controller;

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Posicion;

/**
 * Formulario para trasladar una persona de centro y/o delegacion.
 *
 * Migrado desde `apps/personas/controller/traslado_form.php` (slice 5).
 */
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_pau = (int)strtok($a_sel[0], "#");
} else {
    $id_pau = (int)filter_input(INPUT_POST, 'id_pau');
}

$oPersona = Persona::findPersonaEnGlobal($id_pau);
if (!is_object($oPersona)) {
    exit(sprintf(_("No encuentro a nadie con id_nom: %d"), $id_pau));
}
if (get_class($oPersona) === PersonaPub::class) {
    exit(_("con las personas de paso no tiene sentido."));
}

$gesCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
$oDesplCentroDl = new Desplegable();
$oDesplCentroDl->setNombre('new_ctr');
$oDesplCentroDl->setOpciones($gesCentroDl->getArrayCentros("WHERE tipo_ctr !~ '^[(cgi)|(igl)]'"));
$oDesplCentroDl->setBlanco(true);

// False para no incluir la propia dl en la lista.
$oDesplDlyR = Desplegable::desdeOpciones(DelegacionDropdown::listaRegDele(false), 'new_dl');

$SituacionRepository = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($SituacionRepository->getArraySituaciones(traslado: true));
$oDesplSituacion->setNombre('situacion');

$id_ctr = $oPersona->getId_ctr();
$oCentroDl = $gesCentroDl->findById($id_ctr);
$nombre_ctr = $oCentroDl?->getNombre_ubi() ?? '';
$dl = $oPersona->getDl();
$hoy = (new DateTimeLocal())->getFromLocal();

$oHash = new Hash();
$oHash->setCamposForm('new_ctr!f_ctr!new_dl!f_dl!situacion');
$oHash->setArraycamposHidden([
    'obj_pau' => $Qobj_pau,
    'id_pau' => $id_pau,
    'id_ctr_o' => $id_ctr,
    'ctr_o' => $nombre_ctr,
    'dl' => $dl,
]);

$a_parametros = ['pau' => 'p', 'id_nom' => $id_pau, 'obj_pau' => $Qobj_pau];
$gohome = Hash::link(ConfigGlobal::getWeb() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => 'p', 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau];
$godossiers = Hash::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'titulo' => $oPersona->getNombreApellidos(),
    'nombre_ctr' => $nombre_ctr,
    'hoy' => $hoy,
    'dl' => $dl,
    'oDesplDlyR' => $oDesplDlyR,
    'oDesplCentros' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
    'Qcabecera' => $Qcabecera,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('traslado_form.phtml', $a_campos);

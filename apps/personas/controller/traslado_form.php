<?php

use core\ViewPhtml;
use personas\model\entity\GestorSituacion;
use personas\model\entity\Persona;
use ubis\model\entity\CentroDl;
use ubis\model\entity\GestorCentroDl;
use src\ubis\application\services\DelegacionDropdown;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$pau = (string)filter_input(INPUT_POST, 'pau');
$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_pau = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_pau = (integer)filter_input(INPUT_POST, 'id_pau');
}

if (empty($Qobj_pau)) {
    $oPersona = Persona::newPersona($id_pau);
} else {
    $obj = 'personas\\model\\entity\\' . $Qobj_pau;
    $oPersona = new $obj($id_pau);
}

if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_pau en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}

if (get_class($oPersona) === 'personas\model\entity\PersonaEx'
    || get_class($oPersona) === 'personas\model\entity\PersonaIn') {
    exit(_("con las personas de paso no tiene sentido."));
}

$gesCentroDl = new GestorCentroDl();
$sCondicion = "WHERE tipo_ctr !~ '^[(cgi)|(igl)]'";
$oDesplCentroDl = $gesCentroDl->getListaCentros($sCondicion);
$oDesplCentroDl->setNombre('new_ctr');

$oDesplDlyR = DelegacionDropdown::listaRegDele(FALSE, 'new_dl'); // False para no incluir mi propia dl en la lista

$GesSituacion = new GestorSituacion();
$oDesplSituacion = $GesSituacion->getListaSituaciones($traslado = true);
$oDesplSituacion->setNombre("situacion");


$id_ctr = $oPersona->getId_ctr();
$oUbi = new CentroDl($id_ctr);
$nombre_ctr = $oUbi->getNombre_ubi();
$dl = $oPersona->getDl();

$oHoy = new web\DateTimeLocal();
$hoy = $oHoy->getFromLocal();

$oHash = new Hash();
$oHash->setCamposForm('new_ctr!f_ctr!new_dl!f_dl!situacion');
$a_camposHidden = array(
    'id_pau' => $id_pau,
    'id_ctr_o' => $id_ctr,
    'ctr_o' => $nombre_ctr,
    'dl' => $dl,
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_parametros = array('pau' => 'p', 'id_nom' => $id_pau, 'obj_pau' => $Qobj_pau);
$gohome = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros = array('pau' => 'p', 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau);
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros));

$titulo = $oPersona->getNombreApellidos();


$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'titulo' => $titulo,
    'nombre_ctr' => $nombre_ctr,
    'hoy' => $hoy,
    'dl' => $dl,
    'oDesplDlyR' => $oDesplDlyR,
    'oDesplCentros' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
];

$oView = new ViewPhtml('personas\controller');
$oView->renderizar("traslado_form.phtml", $a_campos);

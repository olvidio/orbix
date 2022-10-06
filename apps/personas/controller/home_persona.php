<?php

use ubis\model\entity as ubis;

/**
 * Esta página pone el titulo en el frame superior.
 *
 *
 * @package    delegacion
 * @subpackage    dossiers
 * @author    Daniel Serrabou
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

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');

    $id_sel = array("$id_nom#$id_tabla");
    $oPosicion->addParametro('id_sel', $id_sel);
}

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$obj = 'personas\\model\\entity\\' . $Qobj_pau;
$oPersona = new $obj($id_nom);
$id_tabla = $oPersona->getId_tabla();

if (!empty($id_tabla)) {
    switch ($id_tabla) {
        case "n":
            $Qobj_pau = "PersonaN";
            break;
        case "x":
            $Qobj_pau = "PersonaNax";
            break;
        case "a":
            $Qobj_pau = "PersonaAgd";
            break;
        case "s":
            $Qobj_pau = "PersonaS";
            break;
        case "sssc":
            $Qobj_pau = "PersonaSSSC";
            break;
        case "pn":
        case "pa":
        case "psssc":
            $Qobj_pau = "PersonaEx";
            break;
    }
}

// Si vengo de planning_select u otros, puede que la tabla sea más genérica (p_de_casa) y no sepa como resolver algunas cosas.
if (isset($_SESSION['session_go_to']['sel']['tabla'])) {
    $_SESSION['session_go_to']['sel']['tabla'] = $Qobj_pau;
}

$pau = "p";

/* def variables **/
$select = "";
$select_agd = "";
$select_super = "";
$select_cp = "";
$select_cp_ae = "";
$select_sssc = "";
$select_de_paso = "";
$from = "";

// según sean numerarios...
$obj = 'personas\\model\\entity\\' . $Qobj_pau;
$oPersona = new $obj($id_nom);


$nom = $oPersona->getNombreApellidos();
$dl = $oPersona->getDl();
$lengua = $oPersona->getLengua();
$f_nacimiento = $oPersona->getF_nacimiento()->getFromLocal();
$santo = '';
$celebra = '';
$situacion = $oPersona->getSituacion();
$f_situacion = $oPersona->getF_situacion()->getFromLocal();
$profesion = $oPersona->getProfesion();
$stgr = $oPersona->getStgr();
if ($Qobj_pau != 'PersonaEx' && $Qobj_pau != 'PersonaIn') {
    $id_ctr = $oPersona->getId_ctr();
    $oCentroDl = new ubis\CentroDl($id_ctr);
    $ctr = $oCentroDl->getNombre_ubi();
} else {
    $ctr = '';
}

$a_parametros = array('pau' => $pau, 'id_nom' => $id_nom, 'obj_pau' => $Qobj_pau);
$gohome = web\Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$go_ficha = web\Hash::link('apps/personas/controller/personas_editar.php?' . http_build_query($a_parametros));
$a_parametros = array('pau' => $pau, 'id_pau' => $id_nom, 'obj_pau' => $Qobj_pau);
$godossiers = web\Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros));

$titulo = $nom;

$telfs = '';
$telfs_fijo = $oPersona->telecos_persona($id_nom, "telf", " / ", "*");
$telfs_movil = $oPersona->telecos_persona($id_nom, "móvil", " / ", "*");
if (!empty($telfs_fijo) && !empty($telfs_movil)) {
    $telfs = $telfs_fijo . " / " . $telfs_movil;
} else {
    $telfs .= $telfs_fijo ?? '';
    $telfs .= $telfs_movil ?? '';
}
$mails = '';
$mails = $oPersona->telecos_persona($id_nom, "e-mail", " / ", "*");


$a_campos = [
    'oPosicion' => $oPosicion,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'go_ficha' => $go_ficha,
    'titulo' => $titulo,
    'telfs' => $telfs,
    'mails' => $mails,
    'stgr' => $stgr,
    'profesion' => $profesion,
    'celebra' => $celebra,
    'santo' => $santo,
    'f_nacimiento' => $f_nacimiento,
    'dl' => $dl,
    'ctr' => $ctr,
    'pau' => $pau,
    'id_pau' => $id_nom,
    'Qobj_pau' => $Qobj_pau
];

$oView = new core\View('personas/controller');
echo $oView->render('home_persona.phtml', $a_campos);
<?php

use core\ViewPhtml;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;

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

switch ($Qobj_pau) {
    case 'PersonaN':
        $repoPersona = $GLOBALS['container']->get(PersonaNRepositoryInterface::class);
        break;
    case 'PersonaNax':
        $repoPersona = $GLOBALS['container']->get(PersonaNaxRepositoryInterface::class);
        break;
    case 'PersonaAgd':
        $repoPersona = $GLOBALS['container']->get(PersonaAgdRepositoryInterface::class);
        break;
    case 'PersonaS':
        $repoPersona = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        break;
    case 'PersonaSSSC':
        $repoPersona = $GLOBALS['container']->get(PersonaSSSCRepositoryInterface::class);
        break;
    case 'PersonaEx':
        $repoPersona = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        break;
    default:
        echo "No existe la clase de la persona";
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

$oPersona = $repoPersona->findById($id_nom);
$nom = $oPersona->getNombreApellidos();
$dl = $oPersona->getDl();
$lengua = $oPersona->getIdioma_preferido();
$f_nacimiento = $oPersona->getF_nacimiento()?->getFromLocal();
$santo = '';
$celebra = '';
$situacion = $oPersona->getSituacion();
$f_situacion = $oPersona->getF_situacion()?->getFromLocal();
$profesion = $oPersona->getProfesion();
$stgr = $oPersona->getNivel_stgr();
if ($Qobj_pau !== 'PersonaEx' && $Qobj_pau !== 'PersonaIn') {
    $id_ctr = $oPersona->getId_ctr();
    $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $oCentroDl = $CentroDlRepository->findById($id_ctr);
    $ctr = $oCentroDl->getNombre_ubi();
} else {
    $ctr = '';
}

$a_parametros = array('pau' => $pau, 'id_nom' => $id_nom, 'obj_pau' => $Qobj_pau);
$gohome = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$go_ficha = Hash::link('apps/personas/controller/personas_editar.php?' . http_build_query($a_parametros));
$a_parametros = array('pau' => $pau, 'id_pau' => $id_nom, 'obj_pau' => $Qobj_pau);
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros));

$titulo = $nom;

$telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
$telfs = '';
$telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', " / ", "*");
$telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', " / ", "*");
if (!empty($telfs_fijo) && !empty($telfs_movil)) {
    $telfs = $telfs_fijo . " / " . $telfs_movil;
} else {
    $telfs .= $telfs_fijo ?? '';
    $telfs .= $telfs_movil ?? '';
}
$mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', " / ", "*");


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

$oView = new ViewPhtml('personas\controller');
$oView->renderizar('home_persona.phtml', $a_campos);
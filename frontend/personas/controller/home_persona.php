<?php

namespace frontend\personas\controller;

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Posicion;

/**
 * Pantalla de cabecera de una persona (datos basicos + acceso a dossiers y ficha).
 *
 * Migrado desde `apps/personas/controller/home_persona.php` (slice 3 del
 * modulo `personas`).
 */
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
} else {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$resolver = new PersonaRepositoryResolver();
try {
    $repoPersona = $resolver->repositorio($Qobj_pau);
} catch (\InvalidArgumentException) {
    echo _("No existe la clase de la persona");
    die();
}

// Si vengo de planning_select u otros, la tabla puede ser mas generica.
if (isset($_SESSION['session_go_to']['sel']['tabla'])) {
    $_SESSION['session_go_to']['sel']['tabla'] = $Qobj_pau;
}

$pau = "p";

$oPersona = $repoPersona->findById($id_nom);
$nom = $oPersona->getNombreApellidos();
$dl = $oPersona->getDl();
$f_nacimiento = $oPersona->getF_nacimiento()?->getFromLocal();
$situacion = $oPersona->getSituacion();
$f_situacion = $oPersona->getF_situacion()?->getFromLocal();
$profesion = $oPersona->getProfesion();
$id_nivel_stgr = $oPersona->getNivel_stgr();
$a_niveles_stgr = NivelStgrId::getArrayNivelStgr();
$stgr = $a_niveles_stgr[$id_nivel_stgr] ?? '';
$observ = $oPersona->getObserv();

// PersonaDl (alias, sin tipar exactamente): normalizar `obj_pau` a la subclase real.
if (get_class($oPersona) === 'src\\personas\\domain\\entity\\PersonaDl') {
    $map = [
        'n' => 'PersonaN',
        'a' => 'PersonaAgd',
        's' => 'PersonaS',
        'sssc' => 'PersonaSSSC',
    ];
    $Qobj_pau = $map[$oPersona->getId_tabla()] ?? $Qobj_pau;
}

$ctr = '';
if ($Qobj_pau !== 'PersonaEx' && $Qobj_pau !== 'PersonaIn') {
    $id_ctr = $oPersona->getId_ctr();
    $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $oCentroDl = $CentroDlRepository->findById($id_ctr);
    $ctr = $oCentroDl?->getNombre_ubi() ?? '';
}

$telecoService = $GLOBALS['container']->get(TelecoPersonaService::class);
$telfs_fijo = $telecoService->getTelecosPorTipo($id_nom, 'telf', " / ", "*");
$telfs_movil = $telecoService->getTelecosPorTipo($id_nom, 'móvil', " / ", "*");
if (!empty($telfs_fijo) && !empty($telfs_movil)) {
    $telfs = $telfs_fijo . " / " . $telfs_movil;
} else {
    $telfs = ($telfs_fijo ?? '') . ($telfs_movil ?? '');
}
$mails = $telecoService->getTelecosPorTipo($id_nom, 'e-mail', " / ", "*");

$a_parametros = ['pau' => $pau, 'id_nom' => $id_nom, 'obj_pau' => $Qobj_pau];
$gohome = Hash::link(ConfigGlobal::getWeb() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$go_ficha = Hash::link(ConfigGlobal::getWeb() . '/frontend/personas/controller/personas_editar.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => $pau, 'id_pau' => $id_nom, 'obj_pau' => $Qobj_pau];
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'oPosicion' => $oPosicion,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'go_ficha' => $go_ficha,
    'titulo' => $nom,
    'telfs' => $telfs,
    'mails' => $mails,
    'stgr' => $stgr,
    'profesion' => $profesion,
    'celebra' => '',
    'santo' => '',
    'f_nacimiento' => $f_nacimiento,
    'dl' => $dl,
    'ctr' => $ctr,
    'pau' => $pau,
    'id_pau' => $id_nom,
    'Qobj_pau' => $Qobj_pau,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('home_persona.phtml', $a_campos);

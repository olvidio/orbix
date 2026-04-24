<?php

namespace frontend\personas\controller;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\value_objects\IncCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Posicion;

/**
 * Ficha de una persona: edicion (o alta si `$Qnuevo === 1`).
 *
 * Migrado desde `apps/personas/controller/personas_editar.php` (slice 3 del
 * modulo `personas`).
 */
require_once("apps/core/global_header.inc");
require_once("apps/web/func_web.php");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */

$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo'); // 0 -> existe, 1 -> nuevo
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$resolver = new PersonaRepositoryResolver();
try {
    $repoPersona = $resolver->repositorio($Qobj_pau);
} catch (\InvalidArgumentException) {
    echo _("No existe la clase de la persona");
    die();
}
$obj = 'src\\personas\\domain\\entity\\' . $Qobj_pau;

$oPosicion->recordar();

$trato = '';
$nom = '';
$apel_fam = '';
$nx1 = '';
$apellido1 = '';
$nx2 = '';
$apellido2 = '';
$lugar_nacimiento = '';
$f_nacimiento = '';
$f_situacion = '';
$profesion = '';
$sacd = '';
$eap = '';
$inc = '';
$f_inc = '';
$ce = '';
$ce_lugar = '';
$ce_ini = '';
$ce_fin = '';
$observ = '';
$titulo = '';
$nom_ctr = '';
$id_ctr = '';
$gohome = '';
$godossiers = '';
$ir_a_traslado = '';
$oDesplCentroDl = [];

if (!empty($Qnuevo)) {
    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    $apellido1 = urldecode($Qapellido1);
    $f_situacion = (new DateTimeLocal())->getFromLocal();
    $id_tabla = (string)filter_input(INPUT_POST, 'tabla');
    $situacion = 'A';
    $idioma_preferido = ConfigGlobal::mi_idioma();
    $dl = ConfigGlobal::mi_delef();

    $newIdAuto = $repoPersona->getNewId();
    $Qid_nom = $repoPersona->getNewIdNom($newIdAuto);
    $nivel_stgr = '';
    $titulo = $apellido1;
} else {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) {
        $Qid_nom = (int)strtok($a_sel[0], "#");
        $id_tabla = (string)strtok("#");
    } else {
        $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
        $id_tabla = (string)filter_input(INPUT_POST, 'tabla');
    }
    // Si vengo por Posicion, borro la ultima.
    $stack = isset($_POST['stack']) ? filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : '';
    if ($stack !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $oPosicion2->olvidar($stack);
        }
    }

    $oPersona = $repoPersona->findById($Qid_nom);
    $id_tabla = $oPersona->getId_tabla();
    $dl = $oPersona->getDl();
    $nivel_stgr = $oPersona->getNivel_stgr();
    $id_ctr = method_exists($oPersona, 'getId_ctr') ? $oPersona->getId_ctr() : '';
    $situacion = $oPersona->getSituacion();
    $idioma_preferido = $oPersona->getIdioma_preferido();
    $trato = $oPersona->getTrato();
    $nom = $oPersona->getNom();
    $apel_fam = $oPersona->getApel_fam();
    $nx1 = $oPersona->getNx1();
    $apellido1 = $oPersona->getApellido1();
    $nx2 = $oPersona->getNx2();
    $apellido2 = $oPersona->getApellido2();
    $lugar_nacimiento = $oPersona->getLugar_nacimiento();
    $f_nacimiento = $oPersona->getF_nacimiento()?->getFromLocal();
    $f_situacion = $oPersona->getF_situacion()?->getFromLocal();
    $profesion = $oPersona->getProfesion();
    $sacd = $oPersona->isSacd();
    $eap = $oPersona->getEap();
    $inc = $oPersona->getInc();
    $f_inc = $oPersona->getF_inc()?->getFromLocal();
    $ce = $oPersona->getCe();
    $ce_lugar = $oPersona->getCe_lugar();
    $ce_ini = $oPersona->getCe_ini();
    $ce_fin = $oPersona->getCe_fin();
    $observ = $oPersona->getObserv();
    if (!empty($id_ctr)) {
        $centroRepoIface = ConfigGlobal::mi_ambito() === 'rstgr'
            ? CentroRepositoryInterface::class
            : CentroDlRepositoryInterface::class;
        $CentroDlRepository = $GLOBALS['container']->get($centroRepoIface);
        $oCentroDl = $CentroDlRepository->findById($id_ctr);
        $nom_ctr = $oCentroDl?->getNombre_ubi() ?? '';
    }
    $titulo = $oPersona->getNombreApellidos();
}

// Lista de delegaciones.
$repoDl = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
$cDeleg = $repoDl->getDelegaciones(['active' => true, '_ordre' => 'dl']);
$a_dl_todas = [];
if (is_array($cDeleg)) {
    foreach ($cDeleg as $oDeleg) {
        $dl_sigla = $oDeleg->getDlVo()->value();
        $a_dl_todas[$dl_sigla] = $dl_sigla;
    }
}
// Nuevo "de paso": solo delegaciones sin esquema propio.
if ($Qnuevo === 1 && $Qobj_pau === 'PersonaEx') {
    $oDBPropiedades = new DBPropiedades();
    $a_dl_esquemas = $oDBPropiedades->array_posibles_dl_de_esquemas(true);
    $a_dl = array_diff_key($a_dl_todas, $a_dl_esquemas);
} else {
    $a_dl = $a_dl_todas;
}
$oDesplDl = new Desplegable();
$oDesplDl->setNombre('dl');
$oDesplDl->setOpciones($a_dl);
$oDesplDl->setOpcion_sel($dl);
$oDesplDl->setBlanco(true);

if (empty($nom_ctr)) {
    $GesCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $oDesplCentroDl = new Desplegable();
    $oDesplCentroDl->setOpciones($GesCentroDl->getArrayCentros());
    $oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
    $oDesplCentroDl->setNombre("id_ctr");
    $oDesplCentroDl->setBlanco(true);
}

$ok = 0;
$ok_txt = 0;
$presentacion = 'persona_form.phtml';
switch ($Qobj_pau) {
    case 'PersonaAgd':
        $id_tabla = 'a';
        if ($_SESSION['oPerm']->have_perm_oficina('agd')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('agd') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaN':
        $id_tabla = 'n';
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaNax':
        $id_tabla = 'x';
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaS':
        $id_tabla = 's';
        if ($_SESSION['oPerm']->have_perm_oficina('sg')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sg') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaSSSC':
        $id_tabla = 'sssc';
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) $ok = 1;
        $autorizado = $_SESSION['oPerm']->have_perm_oficina('des')
            || $_SESSION['oPerm']->have_perm_oficina('vcsd')
            || $_SESSION['oPerm']->have_perm_oficina('dtor');
        $presentacion = $autorizado ? 'persona_sss_form.phtml' : 'p_public_personas.phtml';
        if ($autorizado) $ok_txt = 1;
        break;
    case 'PersonaEx':
        if (empty($id_tabla)) $id_tabla = 'pn';
        $presentacion = 'persona_de_paso.phtml';
        if (
            $_SESSION['oPerm']->have_perm_oficina('agd') ||
            $_SESSION['oPerm']->have_perm_oficina('sm') ||
            $_SESSION['oPerm']->have_perm_oficina('des') ||
            $_SESSION['oPerm']->have_perm_oficina('est')
        ) {
            $ok = 1;
        }
        $ok_txt = 1;
        break;
}

if (empty($Qnuevo)) {
    $ir_a_traslado = Hash::link(
        ConfigGlobal::getWeb() . '/frontend/personas/controller/traslado_form.php?'
        . http_build_query(['pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau])
    );
}

/*
 * botones:
 *   1: guardar cambios
 *   2: eliminar
 *   3: formato texto
 */
$botones = 0;
if ($ok === 1) {
    $botones = '1';
    if ($Qobj_pau === 'PersonaEx') {
        $botones .= ',2';
    }
}

$SituacionRepository = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($SituacionRepository->getArraySituaciones());
$oDesplSituacion->setNombre("situacion");
$oDesplSituacion->setOpcion_sel($situacion);

$Localrepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$oDesplLengua = new Desplegable();
$oDesplLengua->setOpciones($Localrepository->getArrayLocales());
$oDesplLengua->setNombre('idioma_preferido');
$oDesplLengua->setOpcion_sel($idioma_preferido);

$oDesplStgr = new Desplegable();
$oDesplStgr->setNombre('nivel_stgr');
$oDesplStgr->setOpciones(NivelStgrId::getArrayNivelStgr());
$oDesplStgr->setOpcion_sel($nivel_stgr);
$oDesplStgr->setBlanco(true);

$oDesplInc = new Desplegable();
$oDesplInc->setNombre('inc');
$oDesplInc->setOpciones(IncCode::getArrayIncCode());
$oDesplInc->setOpcion_sel($inc);
$oDesplInc->setBlanco(true);

$oHash = new Hash();
$campos_chk = 'sacd';
$camposForm = 'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin';
if ($Qobj_pau === 'PersonaSSSC') {
    $camposForm = 'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento';
}
if ($Qobj_pau === 'PersonaEx') {
    $campos_chk = 'sacd!profesor_stgr';
    $camposForm = 'id_tabla!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!lugar_nacimiento!edad!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato';
}
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo($campos_chk);
$oHash->setArraycamposHidden([
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_nom' => $Qid_nom,
]);

$a_parametros = ['pau' => 'p', 'id_nom' => $Qid_nom, 'obj_pau' => $Qobj_pau];
$gohome = Hash::link(ConfigGlobal::getWeb() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau];
$godossiers = Hash::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'obj_txt' => $obj,
    'oPosicion' => $oPosicion,
    'pau' => 'p',
    'id_pau' => $Qid_nom,
    'Qobj_pau' => $Qobj_pau,
    'nuevo' => $Qnuevo,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'ir_a_traslado' => $ir_a_traslado,
    'titulo' => $titulo,
    'oHash' => $oHash,
    'id_nom' => $Qid_nom,
    'id_tabla' => $id_tabla,
    'dl' => $dl,
    'id_ctr' => $id_ctr,
    'nom_ctr' => $nom_ctr,
    'oDesplDl' => $oDesplDl,
    'oDesplCentro' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
    'oDesplLengua' => $oDesplLengua,
    'oDesplStgr' => $oDesplStgr,
    'oDesplInc' => $oDesplInc,
    'trato' => $trato,
    'nom' => $nom,
    'apel_fam' => $apel_fam,
    'nx1' => $nx1,
    'apellido1' => $apellido1,
    'nx2' => $nx2,
    'apellido2' => $apellido2,
    'lugar_nacimiento' => $lugar_nacimiento,
    'f_nacimiento' => $f_nacimiento,
    'f_situacion' => $f_situacion,
    'profesion' => $profesion,
    'sacd' => $sacd,
    'eap' => $eap,
    'inc' => $inc,
    'f_inc' => $f_inc,
    'ce' => $ce,
    'ce_lugar' => $ce_lugar,
    'ce_ini' => $ce_ini,
    'ce_fin' => $ce_fin,
    'observ' => $observ,
    'botones' => $botones,
    'obj' => $obj,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar($presentacion, $a_campos);

<?php

use core\ConfigGlobal;
use core\DBPropiedades;
use core\ViewPhtml;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\Desplegable;
use web\Hash;

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
require_once("apps/web/func_web.php");

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qnuevo = (integer)filter_input(INPUT_POST, 'nuevo'); // 0 -> existe, 1->nuevo
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
}
$obj = 'src\\personas\\model\\entity\\' . $Qobj_pau;

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


if (!empty($Qnuevo)) {
    $oF_hoy = new web\DateTimeLocal();
    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    // para los acentos
    $Qapellido1 = urldecode($Qapellido1);
    $oPersona = new $obj;
    $cDatosCampo = $oPersona->getDatosCampos();
    $oDbl = $oPersona->getoDbl();
    foreach ($cDatosCampo as $oDatosCampo) {
        $camp = $oDatosCampo->getNom_camp();
        $valor_predeterminado = $oDatosCampo->datos_campo($oDbl, 'valor');
        $a_campos[$camp] = $valor_predeterminado;
    }
    $oPersona->setApellido1($Qapellido1);
    $oPersona->setF_situacion($oF_hoy);
    $id_tabla = (string)filter_input(INPUT_POST, 'tabla');
    $nivel_stgr = '';
    $dl = ConfigGlobal::mi_delef();
    $nom_ctr = '';
    $id_ctr = '';
    $Qid_nom = '';
    $gohome = '';
    $godossiers = '';
    $ir_a_traslado = '';
    $titulo = '';
} else {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $id_tabla = (string)strtok("#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    } else {
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
        $id_tabla = (string)filter_input(INPUT_POST, 'tabla');
    }
    // Sobre-escribe el scroll_id que se pueda tener
    if (isset($_POST['stack'])) {
        $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $stack = '';
    }
    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }

    $oPersona = $repoPersona->findById($Qid_nom);

    $id_tabla = $oPersona->getId_tabla();
    $dl = $oPersona->getDl();
    $nivel_stgr = $oPersona->getNivel_stgr();
    // los de paso no tienen ctr
    if (method_exists($oPersona, "getId_ctr")) {
        $id_ctr = $oPersona->getId_ctr();
    } else {
        $id_ctr = '';
    }

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
    $f_nacimiento = $oPersona->getF_nacimiento()->getFromLocal();
    $f_situacion = $oPersona->getF_situacion()->getFromLocal();
    $profesion = $oPersona->getProfesion();
    $sacd = $oPersona->isSacd();
    $eap = $oPersona->getEap();
    $inc = $oPersona->getInc();
    $f_inc = $oPersona->getF_inc()->getFromLocal();
    $ce = $oPersona->getCe();
    $ce_lugar = $oPersona->getCe_lugar();
    $ce_ini = $oPersona->getCe_ini();
    $ce_fin = $oPersona->getCe_fin();
    $observ = $oPersona->getObserv();


//	// para los de paso
//	if (method_exists($oPersona, "getEdad")) {
//		$edad = $oPersona->getEdad();
//	} else {
//		$edad = '';
//	}
//	if (method_exists($oPersona, "getProfesor_stgr")) {
//		$profesor_stgr = $oPersona->getProfesor_stgr();
//	} else {
//		$profesor_stgr = '';
//	}
    // para el ctr hay que buscar el nombre
    if (!empty($id_ctr)) {
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $CentroDlRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        } else {
            $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        }
        $oCentroDl = $CentroDlRepository->findById($id_ctr);
        $nom_ctr = $oCentroDl->getNombre_ubi();
        $oDesplCentroDl = [];
    } else {
        $nom_ctr = '';
    }
}

// para la dl
$repoDl = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
$cDeleg = $repoDl->getDelegaciones(['active' => true, '_ordre' => 'dl']);
$a_dl_todas = [];
if (is_array($cDeleg)) {
    foreach ($cDeleg as $oDeleg) {
        $dl_sigla = $oDeleg->getDlVo()->value();
        $a_dl_todas[$dl_sigla] = $dl_sigla;
    }
}

// si es nuevo de paso, solamente permito las dl que no están en aquinate.
if ($Qnuevo === 1 && $Qobj_pau === 'PersonaEx') {
    $oDBPropiedades = new DBPropiedades();
    $a_dl_esquemas = $oDBPropiedades->array_posibles_dl_de_esquemas(TRUE);
    $a_dl = array_diff_key($a_dl_todas, $a_dl_esquemas);
} else {
    $a_dl = $a_dl_todas;
}

//$oDesplDl = $gesDl->getListaDelegaciones();
$oDesplDl = new Desplegable();
$oDesplDl->setNombre('dl');
$oDesplDl->setOpciones($a_dl);
$oDesplDl->setOpcion_sel($dl);
$oDesplDl->setBlanco(TRUE);


// para el ctr, si es nuevo o está vacío
if (empty($nom_ctr)) {
    $GesCentroDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $aOpciones = $GesCentroDl->getArrayCentros();
    $oDesplCentroDl= new Desplegable();
    $oDesplCentroDl->setOpciones($aOpciones);
    $oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
    $oDesplCentroDl->setNombre("id_ctr");
}

$ok = 0;
$ok_txt = 0;
$presentacion = "persona_form.phtml";
switch ($Qobj_pau) {
    case "PersonaAgd":
        $id_tabla = 'a';
        if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
            $ok = 1;
        }
        if (($_SESSION['oPerm']->have_perm_oficina('agd') || $_SESSION['oPerm']->have_perm_oficina('dtor'))) {
            //$presentacion="p_agregados.phtml";
            $presentacion = "persona_form.phtml";
            $ok_txt = 1;
        } else {
            $presentacion = "p_public_personas.phtml";
        }
        break;
    case "PersonaN":
        $id_tabla = 'n';
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
            $ok = 1;
        }
        if (($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor'))) {
            //$presentacion="p_numerarios.phtml";
            $presentacion = "persona_form.phtml";
            $ok_txt = 1;
        } else {
            $presentacion = "p_public_personas.phtml";
        }
        break;
    case "PersonaNax":
        $id_tabla = 'x';
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
            $ok = 1;
        }
        if ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor')) {
            //$presentacion="p_numerarios.phtml";
            $presentacion = "persona_form.phtml";
            $ok_txt = 1;
        } else {
            $presentacion = "p_public_personas.phtml";
        }
        break;
    case "PersonaS":
        $id_tabla = 's';
        if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
            $ok = 1;
        }
        if ($_SESSION['oPerm']->have_perm_oficina('sg') || $_SESSION['oPerm']->have_perm_oficina('dtor')) {
            //$presentacion="p_supernumerarios.phtml";
            $presentacion = "persona_form.phtml";
            $ok_txt = 1;
        } else {
            $presentacion = "p_public_personas.phtml";
        }
        break;
    case "PersonaSSSC":
        $id_tabla = 'sssc';
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
            $ok = 1;
        }
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('dtor')) {
            //$presentacion="p_sssc.phtml";
            $presentacion = "persona_sss_form.phtml";
            $ok_txt = 1;
        } else {
            $presentacion = "p_public_personas.phtml";
        }
        break;
    case "PersonaEx":
        if (empty($id_tabla)) $id_tabla = 'pn';
        $presentacion = "persona_de_paso.phtml";
        if ($_SESSION['oPerm']->have_perm_oficina('agd') || $_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('est')) {
            $ok = 1;
        }
        $ok_txt = 1;
        break;
}

if (empty($Qnuevo)) {
    $ir_a_traslado = Hash::link('apps/personas/controller/traslado_form.php?' . http_build_query(array('pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau)));
}

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: formato texto
*/
if ($ok == 1) {
    $botones = '1';
    // de momento se lo permito a los de paso i cp
    if ($Qobj_pau === 'PersonaEx') {
        $botones .= ',2';
    }
}
if ($ok_txt == 1) {
    //$botones .= ',3'; // de momento no lo pongo
}

//------------------------------------------------------------------------

$SituacionRepository = $GLOBALS['container']->get(SituacionRepositoryInterface::class);
$aOpciones = $SituacionRepository->getArraySituaciones();
$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($aOpciones);
$oDesplSituacion->setNombre("situacion");
$oDesplSituacion->setOpcion_sel($situacion);

$Localrepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $Localrepository->getArrayLocales();
$oDesplLengua = new Desplegable();
$oDesplLengua->setOpciones($a_locales);
$oDesplLengua->setNombre('idioma_preferido');
$oDesplLengua->setOpcion_sel($idioma_preferido);

//posibles valores de stgr
$NivelStgrRepository = $GLOBALS['container']->get(NivelStgrRepositoryInterface::class);
$aTipos_stgr = $NivelStgrRepository->getArrayNivelesStgr();
$oDesplStgr = new Desplegable();
$oDesplStgr->setNombre('nivel_stgr');
$oDesplStgr->setOpciones($aTipos_stgr);
$oDesplStgr->setOpcion_sel($nivel_stgr);
$oDesplStgr->setBlanco(true);

$oHash = new Hash();
$campos_chk = 'sacd';
$camposForm = 'que!id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin';

//Para la presentacion "de_sss" los campos un poco distintos:
if ($Qobj_pau === 'PersonaSSSC') {
    $camposForm = 'que!id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento';
}
//Para la presentacion "de_paso" los campos un poco distintos:
if ($Qobj_pau === 'PersonaEx') {
    $campos_chk = 'sacd!profesor_stgr';
    $camposForm = 'que!id_tabla!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!lugar_nacimiento!edad!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato';
}

$oHash->setCamposForm($camposForm);
$oHash->setcamposNo($campos_chk);
$a_camposHidden = array(
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_nom' => $Qid_nom
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_parametros = array('pau' => 'p', 'id_nom' => $Qid_nom, 'obj_pau' => $Qobj_pau);
$gohome = Hash::link('apps/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros = array('pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau);
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros));

$titulo = $oPersona->getNombreApellidos();


$a_campos = ['obj_txt' => $obj,
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
];

$oView = new ViewPhtml('personas\controller');
$oView->renderizar($presentacion, $a_campos);
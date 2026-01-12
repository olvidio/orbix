<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\domain\ProfesorActividad;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$obj = 'ActividadAsignatura';

$oPosicion->recordar();

$Qpau = (string)filter_input(INPUT_POST, 'pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_activ = (integer)strtok($a_sel[0], "#");
    $Qid_asignatura = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    if ($Qpau === 'a') {
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
//	$Qid_activ = (integer) filter_input(INPUT_POST, 'id_activ');
}

$chk_avisado = '';
$chk_confirmado = '';
$chk_preceptor = '';
$oDesplAsignaturas = [];

if (!empty($Qid_asignatura)) { //caso de modificar
    $mod = "editar";

    $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
    $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);

    $ProfesorAsignaturaService = $GLOBALS['container']->get(ProfesorAsignaturaService::class);
    $aOpciones = $ProfesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($Qid_asignatura));
    $oDesplProfesores = new Desplegable();
    $oDesplProfesores->setOpciones($aOpciones);

    $id_profesor = $oActividadAsignatura->getId_profesor();
    if (!empty($id_profesor)) {
        $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
        $aOpciones = $ProfesorStgrService->getArrayProfesoresPub();
        $oDesplProfesores->setOpciones($aOpciones);
        $oDesplProfesores->setOpcion_sel($id_profesor);
    }

    $aviso = $oActividadAsignatura->getAvis_profesor();
    $chk_avisado = ($aviso === "a") ? "selected" : '';
    $chk_confirmado = ($aviso === "c") ? "selected" : '';
    $tipo = $oActividadAsignatura->getTipo();
    $chk_preceptor = ($tipo === "p") ? "selected" : '';
    $f_ini = $oActividadAsignatura->getF_ini()?->getFromLocal();
    $f_fin = $oActividadAsignatura->getF_fin()?->getFromLocal();

    $oAsignatura = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class)->findById($Qid_asignatura);
    if ($oAsignatura === null) {
        throw new Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $Qid_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();

    $primary_key_s = "id_activ=$Qid_activ AND id_asignatura=$Qid_asignatura";
} else { //caso de nueva asignatura
    $mod = "nuevo";
    $nombre_corto = '';
    $ProfesorActividad = new ProfesorActividad();
    $aOpciones = $ProfesorActividad->getArrayProfesoresActividad(array($Qid_activ));
    $oDesplProfesores = new Desplegable();
    $oDesplProfesores->setOpciones($aOpciones);
    $oDesplProfesores->setBlanco(true);
    $oDesplProfesores->setOpcion_sel(-1);

    $f_ini = '';
    $f_fin = '';
    if (!empty($Qid_activ)) {
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aOpciones = $AsignaturaRepository->getArrayAsignaturasConSeparador(false);
        $oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
        $oDesplAsignaturas->setNombre('id_asignatura');
        $oDesplAsignaturas->setAction("fnjs_mas_profes('asignatura')");
    } else {
        exit (_("debería haber un nombre de asignatura"));
    }
}

$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');

$oHash = new Hash();
$camposForm = 'f_ini!f_fin!tipo!id_profesor';
$oHash->setCamposNo('mod!avis_profesor');
$a_camposHidden = array(
    'id_activ' => $Qid_activ,
);
if (!empty($Qid_asignatura)) {
    $a_camposHidden['id_asignatura'] = $Qid_asignatura;
    $a_camposHidden['primary_key_s'] = $primary_key_s;
} else {
    $camposForm .= '!id_asignatura';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);


$oHashTipo = new Hash();
$oHashTipo->setUrl('apps/actividadestudios/controller/lista_profesores_ajax.php');
$oHashTipo->setCamposForm('salida');
$h = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ');
$h1 = $oHashTipo->linkSinVal();

$oHashTipo->setCamposForm('salida!id_activ!id_asignatura');
$h2 = $oHashTipo->linkSinVal();

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'h1' => $h1,
    'h2' => $h2,
    'mod' => $mod,
    'id_activ' => $Qid_activ,
    'id_asignatura' => $Qid_asignatura,
    'nombre_corto' => $nombre_corto,
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'oDesplProfesores' => $oDesplProfesores,
    'chk_preceptor' => $chk_preceptor,
    'chk_avisado' => $chk_avisado,
    'chk_confirmado' => $chk_confirmado,
    'f_ini' => $f_ini,
    'f_fin' => $f_fin,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewPhtml('actividadestudios\controller');
$oView->renderizar('form_3005.phtml', $a_campos);

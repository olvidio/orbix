<?php

/**
 * Form de alta / edicion de una `ActividadAsignatura` desde el dossier
 * `asignaturas_de_una_actividad` (3005).
 *
 * Sucesor de `apps/actividadestudios/controller/form_3005.php`. URL canonica.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\domain\ProfesorActividad;
use frontend\shared\web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$obj = 'ActividadAsignatura';

$oPosicion->recordar();

$Qpau = (string) filter_input(INPUT_POST, 'pau');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_activ = (int) strtok($a_sel[0], "#");
    $Qid_asignatura = (int) strtok("#");
} else {
    if ($Qpau === 'a') {
        $Qid_activ = (int) filter_input(INPUT_POST, 'id_pau');
    } else {
        $Qid_activ = (int) filter_input(INPUT_POST, 'id_activ');
    }
    $Qid_asignatura = (int) filter_input(INPUT_POST, 'id_asignatura');
}

$chk_avisado = '';
$chk_confirmado = '';
$chk_preceptor = '';
$oDesplAsignaturas = [];

if (!empty($Qid_asignatura)) {
    $mod = 'editar';

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
    $chk_avisado = ($aviso === 'a') ? 'selected' : '';
    $chk_confirmado = ($aviso === 'c') ? 'selected' : '';
    $tipo = $oActividadAsignatura->getTipo();
    $chk_preceptor = ($tipo === 'p') ? 'selected' : '';
    $f_ini = $oActividadAsignatura->getF_ini()?->getFromLocal();
    $f_fin = $oActividadAsignatura->getF_fin()?->getFromLocal();

    $oAsignatura = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class)->findById($Qid_asignatura);
    if ($oAsignatura === null) {
        throw new Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $Qid_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    $primary_key_s = "id_activ=$Qid_activ AND id_asignatura=$Qid_asignatura";
} else {
    $mod = 'nuevo';
    $nombre_corto = '';
    $ProfesorActividad = new ProfesorActividad();
    $aOpciones = $ProfesorActividad->getArrayProfesoresActividad([$Qid_activ]);
    $oDesplProfesores = new Desplegable();
    $oDesplProfesores->setOpciones($aOpciones);
    $oDesplProfesores->setBlanco(true);
    $oDesplProfesores->setOpcion_sel(-1);

    $f_ini = '';
    $f_fin = '';
    if (empty($Qid_activ)) {
        exit(_("debería haber un nombre de asignatura"));
    }
    $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
    $aOpciones = $AsignaturaRepository->getArrayAsignaturasConSeparador(false);
    $oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
    $oDesplAsignaturas->setNombre('id_asignatura');
    $oDesplAsignaturas->setAction("fnjs_mas_profes('asignatura')");
}

$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');

$oHash = new Hash();
$camposForm = 'f_ini!f_fin!tipo!id_profesor';
$oHash->setCamposNo('mod!avis_profesor');
$a_camposHidden = [
    'id_activ' => $Qid_activ,
];
if (!empty($Qid_asignatura)) {
    $a_camposHidden['id_asignatura'] = $Qid_asignatura;
    $a_camposHidden['primary_key_s'] = $primary_key_s;
} else {
    $camposForm .= '!id_asignatura';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_profesores = $web . '/src/actividadestudios/profesores_desplegable_data';

$oHashTipo = new Hash();
$oHashTipo->setUrl($url_profesores);
$oHashTipo->setCamposForm('salida');
$h = $oHashTipo->linkSinValParams();

$oHashTipo->setCamposForm('salida!id_activ');
$h1 = $oHashTipo->linkSinValParams();

$oHashTipo->setCamposForm('salida!id_activ!id_asignatura');
$h2 = $oHashTipo->linkSinValParams();

$url_actividad_asignatura_nueva = $web . '/src/actividadestudios/actividad_asignatura_nueva';
$url_actividad_asignatura_editar = $web . '/src/actividadestudios/actividad_asignatura_editar';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'h1' => $h1,
    'h2' => $h2,
    'url_profesores' => $url_profesores,
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
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'url_actividad_asignatura_nueva' => $url_actividad_asignatura_nueva,
    'url_actividad_asignatura_editar' => $url_actividad_asignatura_editar,
];

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('form_asignaturas_de_una_actividad.phtml', $a_campos);

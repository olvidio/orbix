<?php

/**
 * Pantalla del acta de notas para una asignatura concreta de una actividad.
 *
 * Sucesor de `apps/actividadestudios/controller/acta_notas.php`. Incluye
 * `frontend/notas/controller/acta_ver.php` para pintar el form del acta, y
 * debajo la tabla de alumnos matriculados con su nota. Las acciones de
 * guardar borrador / grabar definitivas apuntan a los endpoints nuevos de
 * `src/actividadestudios/`.
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\entity\Persona;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\Posicion;

require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$notas = 1;

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_sel = '';
$Qscroll_id = (string) filter_input(INPUT_POST, 'scroll_id');
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!empty($a_sel)) {
    $id_activ = (int) strtok($a_sel[0], '#');
    $id_asignatura = (int) strtok('#');
} else {
    $id_asignatura = (int) filter_input(INPUT_POST, 'id_asignatura');
    $id_activ = (int) filter_input(INPUT_POST, 'id_activ');
}

$mi_dele = ConfigGlobal::mi_delef();
$permiso = (int) filter_input(INPUT_POST, 'permiso');
$ActividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
$cActivAsignaturas = $ActividadAsignaturaRepository->getActividadAsignaturas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
$oActividadAsignatura = $cActivAsignaturas[0];
$id_schema = $oActividadAsignatura->getId_schema();
$DbSchemaRepository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
$cDbSchemas = $DbSchemaRepository->getDbSchemas(['id' => $id_schema]);
$a_reg = explode('-', $cDbSchemas[0]->getSchema());
$dl_matricula = substr($a_reg[1], 0, -1);
$permiso = ($mi_dele === $dl_matricula) ? 3 : 1;

$aOpciones = NotaSituacion::getArraySituacionTxt();
$oDesplNotas = new Desplegable();
$oDesplNotas->setOpciones($aOpciones);
$oDesplNotas->setNombre('id_situacion[]');

$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($id_activ);
$nom_activ = $oActividad->getNom_activ();

$MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
$cMatriculados = $MatriculaRepository->getMatriculas(['id_asignatura' => $id_asignatura, 'id_activ' => $id_activ]);
$matriculados = count($cMatriculados);
$aPersonasMatriculadas = [];
$msg_err = '';
if ($matriculados > 0) {
    foreach ($cMatriculados as $oMatricula) {
        $id_nom = $oMatricula->getId_nom();
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom";
            continue;
        }
        $nom = $oPersona->getPrefApellidosNombre();
        $aPersonasMatriculadas[$nom] = $oMatricula;
    }
    uksort($aPersonasMatriculadas, 'core\strsinacentocmp');
} else {
    echo _('no hay ninguna persona matriculada de esta asignatura');
}

$Qque = (string) filter_input(INPUT_POST, 'que');
$Qid_pau = (int) filter_input(INPUT_POST, 'id_pau');
$Qopcional = (string) filter_input(INPUT_POST, 'opcional');
$Qprimary_key_s = (string) filter_input(INPUT_POST, 'primary_key_s');
$Qid_nivel = (int) filter_input(INPUT_POST, 'id_nivel');

$ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
$cActas = $ActaRepository->getActas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura, '_ordre' => 'f_acta']);
$acta_principal = '';
$nom_acta = '';
if (is_array($cActas) && !empty($cActas)) {
    $a_actas = [0 => '', NotaSituacion::CURSADA => Nota::getStatusTxt(NotaSituacion::CURSADA)];
    foreach ($cActas as $oActa) {
        $nom_acta = $oActa->getActa();
        $a_actas[$nom_acta] = $oActa->getActa();
    }
    $notas = 'acta';
    $oDesplActas = new Desplegable();
    $oDesplActas->setNombre('acta_nota[]');
    $oDesplActas->setOpciones($a_actas);
    if (count($cActas) === 1) {
        $acta_principal = $nom_acta;
    }
} else {
    $notas = 'nuevo';
    $oDesplActas = new Desplegable();
    $oDesplActas->setOpciones(['primero guardar acta']);
}

$nota_max_default = $_SESSION['oConfig']->getNotaMax();

$oHashNotas = new Hash();
$oHashNotas->setCamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$oHashNotas->setArraycamposHidden([
    'id_pau' => $Qid_pau,
    'id_activ' => $id_activ,
    'opcional' => $Qopcional,
    'primary_key_s' => $Qprimary_key_s,
    'id_asignatura' => $id_asignatura,
    'id_nivel' => $Qid_nivel,
    'matriculados' => $matriculados,
]);

if (!empty($msg_err)) {
    echo $msg_err;
}

$txt_alert_acta = _('primero debe guadar los datos del acta');

// Form del acta (dossier notas). Comparte las variables de scope como antes.
include_once 'frontend/notas/controller/acta_ver.php';

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_matricula_guardar = $web . '/src/actividadestudios/acta_notas_matricula_guardar';
$url_notas_definitivas = $web . '/src/actividadestudios/acta_notas_definitivas_grabar';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashNotas' => $oHashNotas,
    'permiso' => $permiso,
    'Qque' => $Qque,
    'aPersonasMatriculadas' => $aPersonasMatriculadas,
    'oDesplActas' => $oDesplActas,
    'acta_principal' => $acta_principal,
    'txt_alert_acta' => $txt_alert_acta,
    'nota_max_default' => $nota_max_default,
    'url_matricula_guardar' => $url_matricula_guardar,
    'url_notas_definitivas' => $url_notas_definitivas,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('acta_notas.phtml', $a_campos);

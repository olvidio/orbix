<?php

use core\ConfigGlobal;
use core\ViewPhtml;
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

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$notas = 1; // para indicar a la página de actas que está dentro de ésta.

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_sel = '';
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer)strtok($a_sel[0], "#");
    $id_asignatura = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
    $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}

// los permisos depende de cada asignatura
$mi_dele = ConfigGlobal::mi_delef();
$permiso = (integer)filter_input(INPUT_POST, 'permiso');
$ActividadAsignaturaRepository =$GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
$cActivAsignaturas = $ActividadAsignaturaRepository->getActividadAsignaturas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura));
$oActividadAsignatura = $cActivAsignaturas[0];
$id_schema = $oActividadAsignatura->getId_schema();
$DbSchemaRepository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
$cDbSchemas = $DbSchemaRepository->getDbSchemas(['id' => $id_schema]);
$a_reg = explode('-', $cDbSchemas[0]->getSchema());
$dl_matricula = substr($a_reg[1], 0, -1); // quito la v o la f.
if ($mi_dele === $dl_matricula) {
    $permiso = 3;
} else {
    $permiso = 1;
}

$NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
$aOpciones = $NotaRepository->getArrayNotas();
$oDesplNotas = new Desplegable();
$oDesplNotas->setOpciones($aOpciones);
$oDesplNotas->setNombre('id_situacion[]');

$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($id_activ);
$nom_activ = $oActividad->getNom_activ();

$MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
$cMatriculados = $MatriculaRepository->getMatriculas(array('id_asignatura' => $id_asignatura, 'id_activ' => $id_activ));
$matriculados = count($cMatriculados);
$aPersonasMatriculadas = [];
if ($matriculados > 0) {
    // para ordenar
    $msg_err = '';
    foreach ($cMatriculados as $oMatricula) {
        $id_nom = $oMatricula->getId_nom();
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $nom = $oPersona->getPrefApellidosNombre();
        $aPersonasMatriculadas[$nom] = $oMatricula;
    }
    uksort($aPersonasMatriculadas, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
} else {
    echo _("no hay ninguna persona matriculada de esta asignatura");
}

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qopcional = (string)filter_input(INPUT_POST, 'opcional');
$Qprimary_key_s = (string)filter_input(INPUT_POST, 'primary_key_s');
$Qid_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');

$ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
$cActas = $ActaRepository->getActas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura, '_ordre' => 'f_acta'));
$acta_principal = '';
if (is_array($cActas) && !empty($cActas)) {
    $a_actas = [0 => '', NotaSituacion::CURSADA => Nota::getStatusTxt(NotaSituacion::CURSADA)];
    foreach ($cActas as $oActa) {
        $nom_acta = $oActa->getActa();
        $a_actas[$nom_acta] = $oActa->getActa();
    }
    $notas = "acta"; // para indicar a la página de actas que está dentro de ésta.
    $oDesplActas = new Desplegable();
    $oDesplActas->setNombre('acta_nota[]');
    $oDesplActas->setOpciones($a_actas);
    // Si sólo hay una, la selecciono por defecto.
    if (count($cActas) === 1) {
        $acta_principal = $nom_acta;
    }
} else {
    $notas = "nuevo";// para indicar a la página de actas que está dentro de ésta.
    $oDesplActas = new Desplegable();
    $oDesplActas->setOpciones(array('primero guardar acta'));
}


$nota_max_default = $_SESSION['oConfig']->getNotaMax();

$oHashNotas = new Hash();
$oHashNotas->setCamposForm('id_nom!nota_num!nota_max!form_preceptor!acta_nota');
$oHashNotas->setCamposNo('que');
$a_camposHidden1 = array(
    'id_pau' => $Qid_pau,
    'id_activ' => $id_activ,
    'opcional' => $Qopcional,
    'primary_key_s' => $Qprimary_key_s,
    'id_asignatura' => $id_asignatura,
    'id_nivel' => $Qid_nivel,
    'matriculados' => $matriculados
);
$oHashNotas->setArraycamposHidden($a_camposHidden1);

if (!empty($msg_err)) {
    echo $msg_err;
}

$txt_alert_acta = _("primero debe guadar los datos del acta");

// El formulario del acta:
include_once("apps/notas/controller/acta_ver.php");

$a_campos = ['oPosicion' => $oPosicion,
    'oHashNotas' => $oHashNotas,
    'permiso' => $permiso,
    'Qque' => $Qque,
    'aPersonasMatriculadas' => $aPersonasMatriculadas,
    'oDesplActas' => $oDesplActas,
    'acta_principal' => $acta_principal,
    'txt_alert_acta' => $txt_alert_acta,
    'nota_max_default' => $nota_max_default,
];

$oView = new ViewPhtml('actividadestudios\controller');
$oView->renderizar('acta_notas.phtml', $a_campos);

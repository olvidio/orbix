<?php

/**
 * Form de alta / edicion de una `Matricula` desde los dossiers
 * `matriculas_de_una_persona` (1303) y `matriculas_de_una_actividad` (3103).
 *
 * Sucesor de `apps/actividadestudios/controller/form_1303.php`. URL canonica.
 *
 * @param integer $_POST['id_pau']         id de la persona (cuando pau=p) o actividad
 * @param integer $_POST['id_activ']       id de la actividad
 * @param integer $_POST['id_nivel']       nivel seleccionado
 * @param integer $_POST['id_asignatura']  asignatura seleccionada
 * @param array   $_POST['sel']            checkbox: id_activ#id_asignatura
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\profesores\domain\services\ProfesorStgrService;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$obj = 'actividadestudios\\model\\entity\\Matricula';

$id_asignatura_real = '';

$Qid_nom = (int) filter_input(INPUT_POST, 'id_pau');
$Qid_activ = (int) filter_input(INPUT_POST, 'id_activ');
$Qid_nivel = (int) filter_input(INPUT_POST, 'id_nivel');
$Qid_asignatura = (int) filter_input(INPUT_POST, 'id_asignatura');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_activ = (int) strtok($a_sel[0], "#");
    $id_asignatura_real = (int) strtok("#");
}

$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($Qid_activ);
$nom_activ = $oActividad->getNom_activ();

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

$oDesplProfesores = [];
$oDesplNiveles = [];
$chk_preceptor = '';
$id_preceptor = '';
$nombre_corto = '';
$id_nivel = 0;
$id_asignatura = 0;

$MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
if (!empty($id_asignatura_real)) { // caso de modificar
    $mod = "editar";
    $oMatricula = $MatriculaRepository->findById($Qid_activ, $id_asignatura_real, $Qid_nom);
    $id_situacion = $oMatricula->getId_situacion();
    $preceptor = $oMatricula->isPreceptor();
    $id_preceptor = $oMatricula->getId_preceptor();
    $oAsignatura = $AsignaturaRepository->findById($id_asignatura_real);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura_real));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    $id_nivel = $id_asignatura_real;
    $id_asignatura = $id_asignatura_real;

    $chk_preceptor = ($preceptor === true) ? 'checked' : '';
    if (!empty($id_preceptor)) {
        $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
        $aOpciones = $ProfesorStgrService->getArrayProfesoresDl();
        $oDesplProfesores = new Desplegable();
        $oDesplProfesores->setOpciones($aOpciones);
        $oDesplProfesores->setBlanco(1);
        $oDesplProfesores->setNombre('id_preceptor');
        $oDesplProfesores->setOpcion_sel($id_preceptor);
    }
} else { // caso de nueva asignatura
    $mod = "nuevo";
    // todas las asignaturas < nivel 3000
    $cAsignaturas = $AsignaturaRepository->getAsignaturas(
        ['active' => 't', 'id_nivel' => 3000, '_ordre' => 'id_nivel'],
        ['id_nivel' => '<']
    );
    // Asignaturas superadas
    $aSuperadas = NotaSituacion::getArraySuperadas();
    $cond = implode('|', $aSuperadas);
    $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
    $cAsignaturasSuperadas = $PersonaNotaDBRepository->getPersonaNotas(
        [
            'id_situacion' => $cond,
            'id_nom' => $Qid_nom,
            'id_nivel' => 3000,
            '_ordre' => 'id_nivel',
        ],
        ['id_situacion' => '~', 'id_nivel' => '<']
    );
    $aSuperadas = [];
    foreach ($cAsignaturasSuperadas as $oAsignatura) {
        $aSuperadas[$oAsignatura->getId_nivel()] = $oAsignatura->getId_asignatura();
    }
    // También quito las ya matriculadas
    $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
    $cMatriculas = $MatriculaDlRepository->getMatriculas(['id_nom' => $Qid_nom, 'id_activ' => $Qid_activ]);
    $aMatriculadas = [];
    foreach ($cMatriculas as $oMatricula) {
        $aMatriculadas[$oMatricula->getId_nivel()] = $oMatricula->getId_asignatura();
    }
    // asignaturas posibles
    $aFaltan = [];
    foreach ($cAsignaturas as $oAsignatura) {
        $id_nivel = $oAsignatura->getId_nivel();
        if (array_key_exists($id_nivel, $aSuperadas)) {
            continue;
        }
        if (array_key_exists($id_nivel, $aMatriculadas)) {
            continue;
        }
        $aFaltan[$id_nivel] = $oAsignatura->getNombre_corto();
    }

    $oDesplNiveles = new Desplegable();
    $oDesplNiveles->setNombre('id_nivel');
    $oDesplNiveles->setOpciones($aFaltan);
    $oDesplNiveles->setBlanco(1);
    $oDesplNiveles->setAction('fnjs_cmb_opcional()');
}

// Calcular opcionales genericas para fnjs_cmb_opcional.
$cOpcionalesGenericas = $AsignaturaRepository->getAsignaturas(
    ['active' => 't', 'id_sector' => 1, 'id_nivel' => 3000, '_ordre' => 'nombre_corto'],
    ['id_nivel' => '<']
);
$condicion = '';
foreach ($cOpcionalesGenericas as $oOpcional) {
    $condicion .= 'id==' . $oOpcional->getId_nivel() . ' || ';
}
$condicion_js = substr($condicion, 0, -4);

$oHash = new Hash();
$camposForm = '';
$oHash->setCamposNo('preceptor!id_preceptor');
$a_camposHidden = [
    'id_pau' => $Qid_nom,
    'id_activ' => $Qid_activ,
    'mod' => $mod,
];
if (!empty($id_asignatura_real)) {
    $a_camposHidden['id_asignatura'] = $id_asignatura;
    $a_camposHidden['id_nivel'] = $id_nivel;
} else {
    $camposForm .= 'id_asignatura!id_nivel';
}
$oHash->setCamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$web = rtrim(ConfigGlobal::getWeb(), '/');

$url_posibles_opcionales = $web . '/src/notas/posibles_opcionales_data';
$oHashOpcionales = new Hash();
$oHashOpcionales->setUrl($url_posibles_opcionales);
$oHashOpcionales->setCamposForm('id_nom');
$h_posibles_opcionales = $oHashOpcionales->linkSinValParams();

$url_posibles_preceptores = $web . '/src/notas/posibles_preceptores_data';
$oHashPreceptores = new Hash();
$oHashPreceptores->setUrl($url_posibles_preceptores);
$h_posibles_preceptores = $oHashPreceptores->linkSinVal();

$url_matricula_nueva = $web . '/src/actividadestudios/matricula_nueva';
$url_matricula_editar = $web . '/src/actividadestudios/matricula_editar';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_posibles_opcionales' => $url_posibles_opcionales,
    'h_posibles_opcionales' => $h_posibles_opcionales,
    'url_posibles_preceptores' => $url_posibles_preceptores,
    'h_posibles_preceptores' => $h_posibles_preceptores,
    'url_matricula_nueva' => $url_matricula_nueva,
    'url_matricula_editar' => $url_matricula_editar,
    'condicion_js' => $condicion_js,
    'nom_activ' => $nom_activ,
    'id_asignatura_real' => $id_asignatura_real,
    'nombre_corto' => $nombre_corto,
    'oDesplNiveles' => $oDesplNiveles,
    'chk_preceptor' => $chk_preceptor,
    'id_preceptor' => $id_preceptor,
    'oDesplProfesores' => $oDesplProfesores,
    'mod' => $mod,
];

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('form_matriculas_de_una_persona.phtml', $a_campos);

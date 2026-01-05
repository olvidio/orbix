<?php

use core\ViewPhtml;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\entity\Persona;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer)strtok($a_sel[0], "#");
    $id_asignatura = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$msg_err = '';

// nombre de la actividad
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$oActividad = $ActividadAllRepository->findById($id_activ);
$nom_activ = $oActividad->getNom_activ();

//director de estudios
$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$cCargos = $CargoRepository->getCargos(array('cargo' => 'd.est.'));
$id_cargo = $cCargos[0]->getId_cargo(); // solo hay un cargo de director de estudios.
$ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
$cActividadCargos = $ActividadCargoRepository->getActividadCargos(array('id_activ' => $id_activ, 'id_cargo' => $id_cargo));
if (is_array($cActividadCargos) && count($cActividadCargos) > 0) {
    $id_nom_dtor_est = $cActividadCargos[0]->getId_nom(); // Imagino que sólo hay uno.
} else {
    $id_nom_dtor_est = '';
}

if (empty($id_nom_dtor_est)) {
    $nom_director_est = _("para nombrarlo, ir al dossier de cargos de la actividad");
} else {
    $oPersona = Persona::findPersonaEnGlobal($id_nom_dtor_est);
    if (!is_object($oPersona)) {
        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom_dtor_est en  " . __FILE__ . ": line " . __LINE__;
        $nom_director_est = '';
    } else {
        $nom_director_est = $oPersona->getPrefApellidosNombre();
    }
}

//asignaturas: profesores y preceptores.
// por cada asignatura
$aPreceptores = [];
$aProfesores = [];
$a = 0;
$tipo_old = 0;
$ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
$cActividadAsignaturas = $ActividadAsignaturaDlRepository->getActividadAsignaturas(array('id_activ' => $id_activ, '_ordre' => 'tipo'));
$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
foreach ($cActividadAsignaturas as $oActividadAsignatura) {
    $a++;
    $id_asignatura = $oActividadAsignatura->getId_asignatura();
    $id_profesor = $oActividadAsignatura->getId_profesor();
    $tipo = $oActividadAsignatura->getTipo();

    $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();

    if (!empty($id_profesor)) {
        $oPersona = Persona::findPersonaEnGlobal($id_profesor);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_profesor en  " . __FILE__ . ": line " . __LINE__;
            continue;
        }
        $nom_profesor = $oPersona->getPrefApellidosNombre();
    } else {
        $nom_profesor = '?';
    }

    if ($tipo === "p") {
        $aPreceptores[$a]['nombre_corto'] = $nombre_corto;
        $aPreceptores[$a]['creditos'] = $creditos;
        $aPreceptores[$a]['nom_profesor'] = $nom_profesor;
    } else {
        $aProfesores[$a]['nombre_corto'] = $nombre_corto;
        $aProfesores[$a]['creditos'] = $creditos;
        $aProfesores[$a]['nom_profesor'] = $nom_profesor;
    }
}

//buco los asistentes:
$AsistenteRepository = $GLOBALS['container']->get(AsistenteRepositoryInterface::class);
$cAsistentes = $AsistenteRepository->getAsistentesDeActividad($id_activ);
$a = 0;
$a_old = 0;
$aAlumnos = [];
foreach ($cAsistentes as $oAsistente) {
    if (!$oAsistente->isPropio()) {
        continue;
    }
    $a++;
    $id_nom = $oAsistente->getId_nom();
    $observ_est = $oAsistente->getObserv_est();
    $oPersona = Persona::findPersonaEnGlobal($id_nom);
    if ($oPersona === null) {
        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
        continue;
    }
    $nom_persona = $oPersona->getPrefApellidosNombre();
    $ctr = $oPersona->getCentro_o_dl();
    $stgr = $oPersona->getNivel_stgr();
    // busco las asignaturas de esta persona
    $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
    $cMatriculas = $MatriculaRepository->getMatriculas(array('id_nom' => $id_nom, 'id_activ' => $id_activ));
    // si no tiene asignaturas, miro si está de repaso
    if (is_array($cMatriculas) && count($cMatriculas) == 0) {
        switch ($stgr) {
            case "r":
                $est = _("repaso");
                break;
            case "n":
                $est = _("plan de formación");
                break;
            default:
                $est = '???';
        }
        $aAlumnos[$a]['nom_persona'] = $nom_persona;
        $aAlumnos[$a]['ctr'] = $ctr;
        $aAlumnos[$a]['observ_est'] = $observ_est;
        $aAlumnos[$a]['aAsignaturas'] = $est;
    } else {
        $aAsignaturas = [];
        $i = 0;
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        foreach ($cMatriculas as $oMatricula) {
            $i++;
            $id_asignatura = $oMatricula->getId_asignatura();
            $preceptor = $oMatricula->isPreceptor();

            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();
            $preceptor = is_true($preceptor) ? "(" . _("preceptor") . ")" : '';

            $aAsignaturas[$i]['nombre_corto'] = $nombre_corto;
            $aAsignaturas[$i]['creditos'] = $creditos;
            $aAsignaturas[$i]['preceptor'] = $preceptor;
        }
        $aAlumnos[$a]['nom_persona'] = $nom_persona;
        $aAlumnos[$a]['ctr'] = $ctr;
        $aAlumnos[$a]['observ_est'] = $observ_est;
        $aAlumnos[$a]['aAsignaturas'] = $aAsignaturas;
    }
}

if (!empty($msg_err)) {
    echo $msg_err;
}


$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'aPreceptores' => $aPreceptores,
    'aProfesores' => $aProfesores,
    'aAlumnos' => $aAlumnos,
];

$oView = new ViewPhtml('actividadestudios\controller');
$oView->renderizar('plan_estudios_ca.phtml', $a_campos);
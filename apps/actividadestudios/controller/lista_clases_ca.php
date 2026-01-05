<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\domain\entity\Persona;

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
$dl_org = $oActividad->getDl_org();

//director de estudios
$CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
$cCargos = $CargoRepository->getCargos(array('cargo' => 'd.est.'));
$id_cargo = $cCargos[0]->getId_cargo(); // solo hay un cargo de director de estudios.
$ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
$cActividadCargos = $ActividadCargoRepository->getActividadCargos(array('id_activ' => $id_activ, 'id_cargo' => $id_cargo));
if (is_array($cActividadCargos) && !empty($cActividadCargos)) {
    $id_nom_dtor_est = $cActividadCargos[0]->getId_nom(); // Imagino que sólo hay uno.
} else {
    $id_nom_dtor_est = '';
}

if (empty($id_nom_dtor_est)) {
    $nom_director_est = "<span class=no_print>" . _("para nombrarlo, ir al dossier de cargos de la actividad") . "</span>";
} else {
    $oPersona = Persona::findPersonaEnGlobal($id_nom_dtor_est);
    if (!is_object($oPersona)) {
        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom_dtor_est en  " . __FILE__ . ": line " . __LINE__;
        $nom_director_est = '';
    } else {
        $nom_director_est = $oPersona->getPrefApellidosNombre();
    }
}

//asignaturas del ca. (profesores y preceptores).
//asignaturas: profesores y preceptores.
// por cada asignatura
$a = 0;
$tipo_old = 0;
$ActividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
$cActividadAsignaturas = $ActividadAsignaturaRepository->getActividadAsignaturas(array('id_activ' => $id_activ));
$datos_asignatura = [];
$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
foreach ($cActividadAsignaturas as $oActividadAsignatura) {
    $a++;
    $id_asignatura = $oActividadAsignatura->getId_asignatura();
    $tipo = $oActividadAsignatura->getTipo();
    $id_profesor = $oActividadAsignatura->getId_profesor();

    $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
    if ($oAsignatura === null) {
        throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
    }
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();
    if (!empty($id_profesor)) {
        $oPersona = Persona::findPersonaEnGlobal($id_profesor);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>No encuentro a nadie con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
            $nom_profesor = '';
        } else {
            $nom_profesor = $oPersona->getPrefApellidosNombre();
        }
    } else {
        $nom_profesor = '';
    }
    if (!empty($tipo) && $tipo === "p") {
        $tipo_profesor = ucfirst(_("preceptor"));
    } else {
        $tipo_profesor = ucfirst(_("profesor"));
    }

    $datos_asignatura[$a]['nom_profesor'] = $nom_profesor;
    $datos_asignatura[$a]['tipo_profesor'] = $tipo_profesor;
    $datos_asignatura[$a]['nombre_corto'] = $nombre_corto;

    // busco las matriculas
    $cMatriculas = $MatriculaRepository->getMatriculas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura));
    $aMatriculados = [];
    foreach ($cMatriculas as $oMatricula) {
        $id_nom = $oMatricula->getId_nom();
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            // Normalmente es gente a la que no tengoo acceso (otra dl),
            // sino soy la dl organizadora no me preocupo:
            if ($dl_org == ConfigGlobal::mi_delef()) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            }
            continue;
        }
        $nom_persona = $oPersona->getPrefApellidosNombre();
        $ctr = $oPersona->getCentro_o_dl();
        $aMatriculados[$nom_persona] = $ctr;
    }
    uksort($aMatriculados, 'core\strsinacentocmp');
    $datos_asignatura[$a]['alumnos'] = $aMatriculados;

}

if (!empty($msg_err)) {
    echo $msg_err;
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'datos_asignatura' => $datos_asignatura,
];

$oView = new ViewPhtml('actividadestudios\controller');
$oView->renderizar('lista_clases_ca.phtml', $a_campos);
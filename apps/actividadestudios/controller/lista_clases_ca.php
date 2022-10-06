<?php

use actividades\model\entity as actividades;
use actividadcargos\model\entity as actividadcargos;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;

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
$oActividad = new actividades\Actividad($id_activ);
$nom_activ = $oActividad->getNom_activ();
$dl_org = $oActividad->getDl_org();

//director de estudios
$GesCargos = new actividadcargos\GestorCargo();
$cCargos = $GesCargos->getCargos(array('cargo' => 'd.est.'));
$id_cargo = $cCargos[0]->getId_cargo(); // solo hay un cargo de director de estudios.
$GesActividadCargos = new actividadcargos\GestorActividadCargo();
$cActividadCargos = $GesActividadCargos->getActividadCargos(array('id_activ' => $id_activ, 'id_cargo' => $id_cargo));
if (is_array($cActividadCargos) && !empty($cActividadCargos)) {
    $id_nom_dtor_est = $cActividadCargos[0]->getId_nom(); // Imagino que sólo hay uno.
} else {
    $id_nom_dtor_est = '';
}

if (empty($id_nom_dtor_est)) {
    $nom_director_est = "<span class=no_print>" . _("para nombrarlo, ir al dossier de cargos de la actividad") . "</span>";
} else {
    $oPersona = personas\Persona::NewPersona($id_nom_dtor_est);
    if (!is_object($oPersona)) {
        $msg_err .= "<br>$oPersona con id_nom: $id_nom_dtor_est en  " . __FILE__ . ": line " . __LINE__;
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
$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignatura();
$cActividadAsignaturas = $GesActividadAsignaturas->getActividadAsignaturas(array('id_activ' => $id_activ));
$datos_asignatura = array();
foreach ($cActividadAsignaturas as $oActividadAsignatura) {
    $a++;
    $id_asignatura = $oActividadAsignatura->getId_asignatura();
    $tipo = $oActividadAsignatura->getTipo();
    $id_profesor = $oActividadAsignatura->getId_profesor();

    $oAsignatura = new asignaturas\Asignatura($id_asignatura);
    $nombre_corto = $oAsignatura->getNombre_corto();
    $creditos = $oAsignatura->getCreditos();
    if (!empty($id_profesor)) {
        $oPersona = personas\Persona::NewPersona($id_profesor);
        if (!is_object($oPersona)) {
            $msg_err .= "<br>$oPersona con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
            $nom_profesor = '';
        } else {
            $nom_profesor = $oPersona->getPrefApellidosNombre();
        }
    } else {
        $nom_profesor = '';
    }
    if (!empty($tipo) && $tipo == "p") {
        $tipo_profesor = ucfirst(_("preceptor"));
    } else {
        $tipo_profesor = ucfirst(_("profesor"));
    }

    $datos_asignatura[$a]['nom_profesor'] = $nom_profesor;
    $datos_asignatura[$a]['tipo_profesor'] = $tipo_profesor;
    $datos_asignatura[$a]['nombre_corto'] = $nombre_corto;

    // busco las matriculas
    $GesMatriculas = new actividadestudios\GestorMatricula();
    $cMatriculas = $GesMatriculas->getMatriculas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura));
    $aMatriculados = array();
    foreach ($cMatriculas as $oMatricula) {
        $id_nom = $oMatricula->getId_nom();
        $oPersona = personas\Persona::NewPersona($id_nom);
        if (!is_object($oPersona)) {
            // Normalmente es gente a la que no tengoo acceso (otra dl),
            // sino soy la dl organizadora no me preocupo:
            if ($dl_org == core\ConfigGlobal::mi_delef()) {
                $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
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

$oView = new core\View('actividadestudios/controller');
echo $oView->render('lista_clases_ca.phtml', $a_campos);
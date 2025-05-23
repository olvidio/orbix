<?php
/**
 * Esta página sirve para matricular a todas las personas.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        28/05/03.
 *
 */

use actividadestudios\model\entity\GestorActividadAsignatura;
use actividadestudios\model\entity\GestorMatriculaDl;
use actividadestudios\model\entity\MatriculaDl;
use asistentes\model\entity\AsistenteDl;
use asistentes\model\entity\GestorAsistenteDl;
use core\ConfigGlobal;
use notas\model\entity\GestorPersonaNotaDB;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaEx;
use personas\model\entity\Persona;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}

$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $any = (int) date('Y') + 1;
} else {
    $any = (int) date('Y');
}
$inicurs_ca = core\curso_est("inicio", $any)->format('Y-m-d');
$fincurs_ca = core\curso_est("fin", $any)->format('Y-m-d');

// no miro los de repaso:
//   " stgr != 'r' ";
// si no hay id_nom, es para todos los alumnos
if (!empty($Qid_nom)) {
    $aWhere['id_nom'] = $Qid_nom;
    $aWhere['stgr'] = 'r';
    $aOperador['stgr'] = '!=';
    // miro si es de paso

    $oPersona = Persona::NewPersona($Qid_nom);
    if (is_string($oPersona)) {
        exit($oPersona);
    }
    $classname = str_replace("personas\\model\\entity\\", '', get_class($oPersona));

    if ($classname === 'PersonaEx') {
        $GesPersonasDePaso = new GestorPersonaEx();
        $cAlumnos = $GesPersonasDePaso->getPersonasEx($aWhere, $aOperador);
    } else {
        $GesPersonasDl = new GestorPersonaDl();
        $cAlumnos = $GesPersonasDl->getPersonasDl($aWhere, $aOperador);
    }
    if (empty($cAlumnos)) {
        $msg = _("está de repaso");
    }
    $modo_aviso = 'alert';
} else {
    // solo para los de la dl
    $aWhere['situacion'] = 'A';
    $aWhere['stgr'] = 'r';
    $aOperador['stgr'] = '!=';
    $GesPersonasDl = new GestorPersonaDl();
    $cAlumnos = $GesPersonasDl->getPersonasDl($aWhere, $aOperador);
    $modo_aviso = '';
}

// para cada persona:
$m = 0;
$aWhere = [];
$aOperadores = [];
// de estudios ca-n, cv-agd
$aWhere['status'] = \actividades\model\entity\ActividadAll::STATUS_ACTUAL;
$aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
$aOperadores['f_ini'] = 'BETWEEN';
$aWhere['id_tipo_activ'] = '^' . ConfigGlobal::mi_sfsv() . '(122)|(222)|(332)';
$aOperadores['id_tipo_activ'] = '~';
foreach ($cAlumnos as $oPersonaDl) {
    $id_nom = $oPersonaDl->getId_nom();
    $cAsistencias = [];
    // después me interesa el id_activ, asi que lo busco primero:
    if (empty($Qid_activ)) {
        $GesAsistentes = new GestorAsistenteDl();
        $aWhereNom = array('id_nom' => $id_nom, 'propio' => 't');
        $aOperadorNom = [];
        $cAsistencias = $GesAsistentes->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhere, $aOperadores);
    } else { // puede ser que ya le pase la actividad
        $oAsistenteDl = new AsistenteDl(array('id_activ' => $Qid_activ, 'id_nom' => $id_nom));
        $oAsistenteDl->DBCarregar();
        $cAsistencias[0] = $oAsistenteDl;
    }
    // si no cursa ningún ca, me salto todo
    switch (count($cAsistencias)) {
        case 0:
            $msg .= addslashes(sprintf(_("no se ha hecho nada con %s no tiene asignado ca"), $oPersonaDl->getPrefApellidosNombre()));
            $msg .= '\n';
            $msg .= '<br>';
            break;
        case 1:
            $oAsistenteDl = current($cAsistencias); // En el caso de varias, el indice es la f_ini (para poder ordenar en otros casos).
            $id_activ_1 = $oAsistenteDl->getId_activ();
            $est_ok = $oAsistenteDl->getEst_ok();
            if ($est_ok != 1) {
                //borro el plan de estudios de esta persona.
                $GesMatriculas = new GestorMatriculaDl();
                $cMatriculas = $GesMatriculas->getMatriculas(array('id_nom' => $id_nom, 'id_activ' => $id_activ_1));
                foreach ($cMatriculas as $oMatricula) {
                    if ($oMatricula->DBEliminar() === false) {
                        echo _("hay un error, no se ha eliminado");
                        echo "\n" . $oMatricula->getErrorTxt();
                    }
                }

                //busco las asignaturas que ya están aprobadas y las pongo en un array.
                $GesPersonaNotas = new GestorPersonaNotaDB();
                $cPersonaNotas = $GesPersonaNotas->getPersonaNotasSuperadas($id_nom);
                $a_aprobadas = [];
                foreach ($cPersonaNotas as $oPersonaNota) {
                    $a_aprobadas[] = $oPersonaNota->getId_asignatura();
                }
                //busco las asignaturas de su ca
                $GesAsignaturasCa = new GestorActividadAsignatura();
                // Ojo. Se ha ido cambiando:
                //  1. que también coja las asignaturas con preceptor...
                //  2. Que no coja las asignaturas con preceptor...
                $cAsignaturasCa = $GesAsignaturasCa->getActividadAsignaturas(array('id_activ' => $id_activ_1, 'tipo' => 'x'), array('tipo' => 'IS NULL'));
                foreach ($cAsignaturasCa as $oActividadAsignatura) {
                    $id_asignatura = $oActividadAsignatura->getId_asignatura();
                    $preceptor = ($oActividadAsignatura->getTipo() === 'p') ? 't' : 'f';
                    // compruebo que no la tenga ya aprobada:
                    if (in_array($id_asignatura, $a_aprobadas)) continue;
                    // Si es una opcional, compruebo que puede hacerla
                    if ($id_asignatura > 3000) {
                        switch (substr($id_asignatura, 1, 1)) {
                            case 1: //opcional sólo de bienio
                                $aWhereNota['id_nom'] = $id_nom;
                                $aWhereNota['id_nivel'] = "'123[012]'";
                                $aOperadorNota['id_nivel'] = '~';
                                $cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota, $aOperadorNota);
                                if (is_array($cPersonaNotas) && count($cPersonaNotas) < 3) {
                                    $oMatricula = new MatriculaDl(array('id_activ' => $id_activ_1, 'id_asignatura' => $id_asignatura, 'id_nom' => $id_nom));
                                    $oMatricula->setPreceptor($preceptor);
                                    if ($oMatricula->DBGuardar() === false) {
                                        echo _("error al guardar la matrícula");
                                    }
                                }
                                break;
                            case 2: //opcional sólo de cuadrienio
                                $aWhereNota['id_nom'] = $id_nom;
                                $aWhereNota['id_nivel'] = "'243[01234]'";
                                $aOperadorNota['id_nivel'] = '~';
                                $cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota, $aOperadorNota);
                                if (is_array($cPersonaNotas) && count($cPersonaNotas) < 5) {
                                    $oMatricula = new MatriculaDl(array('id_activ' => $id_activ_1, 'id_asignatura' => $id_asignatura, 'id_nom' => $id_nom));
                                    $oMatricula->setPreceptor($preceptor);
                                    if ($oMatricula->DBGuardar() === false) {
                                        echo _("error al guardar la matrícula");
                                    }
                                }
                                break;
                            case 3: //opcional de bienio o cuadrienio
                                $aWhereNota['id_nom'] = $id_nom;
                                $aWhereNota['id_nivel'] = "'123[012]|243[01234]'";
                                $aOperadorNota['id_nivel'] = '~';
                                $cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota, $aOperadorNota);
                                if (is_array($cPersonaNotas) && count($cPersonaNotas) < 8) {
                                    $oMatricula = new MatriculaDl(array('id_activ' => $id_activ_1, 'id_asignatura' => $id_asignatura, 'id_nom' => $id_nom));
                                    $oMatricula->setPreceptor($preceptor);
                                    if ($oMatricula->DBGuardar() === false) {
                                        echo _("error al guardar la matrícula");
                                    }
                                }
                                break;
                        }
                    } else {
                        $oMatricula = new MatriculaDl(array('id_activ' => $id_activ_1, 'id_asignatura' => $id_asignatura, 'id_nom' => $id_nom));
                        $oMatricula->setPreceptor($preceptor);
                        if ($oMatricula->DBGuardar() === false) {
                            echo _("error al guardar la matrícula");
                        }
                    }
                    $m++;
                }
                $msg .= addslashes(sprintf(_("%s se ha matriculado de %s asignaturas"), $oPersonaDl->getPrefApellidosNombre(), $m));
                $msg .= "\n";
            } else {
                $msg .= addslashes(sprintf(_("no se ha hecho nada com %s. ya tiene el plan de estudios confirmado"), $oPersonaDl->getPrefApellidosNombre()));
                $msg .= "\n";
            }
            break;
        default:
            $msg .= addslashes(sprintf(_("no se ha hecho nada con %s, tiene asignado más de un ca"), $oPersonaDl->getPrefApellidosNombre()));
            $msg .= "\n";
    }

}

if (empty($msg)) {
    $msg = addslashes(_("no se ha hecho nada"));
}

echo $msg;

<?php

use asignaturas\model\entity\GestorAsignatura;
use notas\model\EditarPersonaNota;
use notas\model\PersonaNota;
use web\DateTimeLocal;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';

$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

if ($Qpau !== "p") {
    exit ("OJO: pau no es de persona");
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nivel = (integer)strtok($a_sel[0], "#");
    $id_asignatura = (integer)strtok("#");
} else {
    $id_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
    $id_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');
}

$id_situacion = (integer)filter_input(INPUT_POST, 'id_situacion');
$acta = (string)filter_input(INPUT_POST, 'acta');
$f_acta = (string)filter_input(INPUT_POST, 'f_acta');
$oF_acta = DateTimeLocal::createFromLocal($f_acta);
$tipo_acta = (integer)filter_input(INPUT_POST, 'tipo_acta');
$preceptor = (string)filter_input(INPUT_POST, 'preceptor');
$id_preceptor = (integer)filter_input(INPUT_POST, 'id_preceptor');
$detalle = (string)filter_input(INPUT_POST, 'detalle');
$epoca = (integer)filter_input(INPUT_POST, 'epoca');
$id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$nota_num = (float)filter_input(INPUT_POST, 'nota_num', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$nota_max = (integer)filter_input(INPUT_POST, 'nota_max');

if ($id_asignatura === 1) {
    $oGesAsignaturas = new GestorAsignatura();
    $cAsignaturas = $oGesAsignaturas->getAsignaturas(array('id_nivel' => $id_nivel));
    if (!is_array($cAsignaturas) || count($cAsignaturas) === 0) {
        $msg_err = sprintf(_("No se encuentra una asignatura para le nivel: %s"), $id_nivel);
        exit ($msg_err);
    }
    $oAsignatura = $cAsignaturas[0]; // sólo debería haber una
    $id_asignatura = $oAsignatura->getId_asignatura();
}

$oPersonaNota = new PersonaNota();
$oPersonaNota->setIdNivel($id_nivel);
$oPersonaNota->setIdAsignatura($id_asignatura);
$oPersonaNota->setIdNom($Qid_pau);
if ($Qmod !== 'eliminar') {
    $oPersonaNota->setIdSituacion($id_situacion);
    $oPersonaNota->setActa($acta);
    $oPersonaNota->setDetalle($detalle);
    $oPersonaNota->setTipoActa($tipo_acta);
    $oPersonaNota->setFActa($oF_acta);
    $oPersonaNota->setPreceptor($preceptor);
    $oPersonaNota->setIdPreceptor($id_preceptor);
    $oPersonaNota->setEpoca($epoca);
    $oPersonaNota->setIdActiv($id_activ);
    $oPersonaNota->setNotaNum($nota_num);
    $oPersonaNota->setNotaMax($nota_max);
}

$oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
switch ($Qmod) {
    case 'eliminar': //------------ BORRAR --------
        $msg_err = $oEditarPersonaNota->eliminar();
        break;
    case 'nuevo': //------------ NUEVO --------
        $oEditarPersonaNota->nuevo();
        break;
    case 'editar':  //------------ EDITAR --------
        // se ataca a la tabla padre 'e_notas', no hace falta saber en que tabla está. Ya lo sabe él
        $id_asignatura_real = (integer)filter_input(INPUT_POST, 'id_asignatura_real');
        $msg_err = $oEditarPersonaNota->editar($id_asignatura_real);
        break;
}


if (!empty($msg_err)) {
    echo $msg_err;
}

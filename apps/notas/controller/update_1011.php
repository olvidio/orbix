<?php

use asignaturas\model\entity as asignaturas;
use dossiers\model\entity as dossiers;
use notas\model\entity as notas;
use personas\model\entity as personas;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qpau == "p") {
        $id_nivel = (integer)strtok($a_sel[0], "#");
        $id_asignatura = (integer)strtok("#");
    }
}

switch ($Qmod) {
    case 'eliminar': //------------ BORRAR --------
        if ($Qpau == "p") {
            if (!empty($Qid_pau) && !empty($id_asignatura) && !empty($id_nivel)) {
                $oPersonaNota = new notas\PersonaNota();
                $oPersonaNota->setId_nom($Qid_pau);
                $oPersonaNota->setId_asignatura($id_asignatura);
                $oPersonaNota->setId_nivel($id_nivel);
                $oPersonaNota->DBCarregar(); //perque agafi els valors que ja té.
                if ($oPersonaNota->DBEliminar() === false) {
                    $msg_err = _("hay un error, no se ha borrado");
                }
            }
        }
        break;
    case 'nuevo': //------------ NUEVO --------
        $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
        $Qid_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');
        //No es una opcional
        if ($Qid_asignatura == '1') {
            $oGesAsignaturas = new asignaturas\GestorAsignatura();
            $cAsignaturas = $oGesAsignaturas->getAsignaturas(array('id_nivel' => $Qid_nivel));
            $oAsignatura = $cAsignaturas[0]; // sólo debería haber una
            $id_asignatura = $oAsignatura->getId_asignatura();
        } else {//es una opcional
            $id_asignatura = $Qid_asignatura;
        }
        $oPersonaNota = new notas\PersonaNota();
        $oPersonaNota->setId_nivel($Qid_nivel);
        $oPersonaNota->setId_asignatura($id_asignatura);
        $oPersonaNota->setId_nom($Qid_pau);
        // para saber a que schema pertenece la persona
        $oPersona = personas\Persona::NewPersona($Qid_pau);
        if (!is_object($oPersona)) {
            $msg_err = "<br>$oPersona con id_nom: $Qid_pau en  " . __FILE__ . ": line " . __LINE__;
            exit($msg_err);
        }
        $id_schema = $oPersona->getId_schema();
        $oPersonaNota->setId_schema($id_schema);

        $Qid_situacion = (integer)filter_input(INPUT_POST, 'id_situacion');
        $Qacta = (string)filter_input(INPUT_POST, 'acta');
        $Qf_acta = (string)filter_input(INPUT_POST, 'f_acta');
        $Qtipo_acta = (integer)filter_input(INPUT_POST, 'tipo_acta');
        $Qpreceptor = (string)filter_input(INPUT_POST, 'preceptor');
        $Qid_preceptor = (integer)filter_input(INPUT_POST, 'id_preceptor');
        $Qdetalle = (string)filter_input(INPUT_POST, 'detalle');
        $Qepoca = (integer)filter_input(INPUT_POST, 'epoca');
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $Qnota_num = (float)filter_input(INPUT_POST, 'nota_num', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $Qnota_max = (integer)filter_input(INPUT_POST, 'nota_max');

        $oPersonaNota->setId_situacion($Qid_situacion);
        $oPersonaNota->setF_acta($Qf_acta);
        $oPersonaNota->setTipo_acta($Qtipo_acta);
        // comprobar valor del acta
        if (!empty($Qacta)) {
            if ($Qtipo_acta == notas\PersonaNota::FORMATO_CERTIFICADO) {
                $oPersonaNota->setActa($Qacta);
            }
            if ($Qtipo_acta == notas\PersonaNota::FORMATO_ACTA) {
                $oActa = new notas\Acta();
                $valor = trim($Qacta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (preg_match($reg_exp, $valor) == 1) {
                } else {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $Qf_acta);
                }
                $oPersonaNota->setActa($valor);
            }
        }
        $oPersonaNota->setPreceptor($Qpreceptor);
        $oPersonaNota->setId_preceptor($Qid_preceptor);
        $oPersonaNota->setDetalle($Qdetalle);
        $oPersonaNota->setEpoca($Qepoca);
        $oPersonaNota->setId_activ($Qid_activ);
        $oPersonaNota->setNota_num($Qnota_num);
        $oPersonaNota->setNota_max($Qnota_max);
        if ($oPersonaNota->DBGuardar() === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
        // si no está abierto, hay que abrir el dossier para esta persona
        //abrir_dossier('p',$_POST['id_pau'],'1303',$oDB);
        $oDossier = new dossiers\Dossier(array('tabla' => 'p', 'id_pau' => $Qid_pau, 'id_tipo_dossier' => 1303));
        $oDossier->abrir();
        $oDossier->DBGuardar();

        break;
    case 'editar':  //------------ EDITAR --------
        $Qid_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');
        $Qid_asignatura_real = (integer)filter_input(INPUT_POST, 'id_asignatura_real');
        if (!empty($Qid_pau) && !empty($Qid_asignatura_real)) {
            $oPersonaNota = new notas\PersonaNota();
            $oPersonaNota->setId_nom($Qid_pau);
            $oPersonaNota->setId_nivel($Qid_nivel);
            $oPersonaNota->DBCarregar(); //perque agafi els valors que ja té.
        } else {
            $oPersonaNota = new notas\PersonaNota();
        }
        $Qid_situacion = (integer)filter_input(INPUT_POST, 'id_situacion');
        $Qacta = (string)filter_input(INPUT_POST, 'acta');
        $Qf_acta = (string)filter_input(INPUT_POST, 'f_acta');
        $Qtipo_acta = (integer)filter_input(INPUT_POST, 'tipo_acta');
        $Qpreceptor = (string)filter_input(INPUT_POST, 'preceptor');
        $Qid_preceptor = (integer)filter_input(INPUT_POST, 'id_preceptor');
        $Qdetalle = (string)filter_input(INPUT_POST, 'detalle');
        $Qepoca = (integer)filter_input(INPUT_POST, 'epoca');
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $Qnota_num = (float)filter_input(INPUT_POST, 'nota_num', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $Qnota_max = (integer)filter_input(INPUT_POST, 'nota_max');

        $oPersonaNota->setId_situacion($Qid_situacion);
        $oPersonaNota->setF_acta($Qf_acta);
        $oPersonaNota->setTipo_acta($Qtipo_acta);
        // comprobar valor del acta
        if (!empty($Qacta)) {
            if ($Qtipo_acta == notas\PersonaNota::FORMATO_CERTIFICADO) {
                $oPersonaNota->setActa($Qacta);
            }
            if ($Qtipo_acta == notas\PersonaNota::FORMATO_ACTA) {
                $oActa = new notas\Acta();
                $valor = trim($Qacta);
                $reg_exp = "/^(\?|\w{1,6}\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
                if (preg_match($reg_exp, $valor) == 1) {
                } else {
                    // inventar acta.
                    $valor = $oActa->inventarActa($valor, $Qf_acta);
                }
                $oPersonaNota->setActa($valor);
            }
        }
        if (empty($Qpreceptor)) {
            $oPersonaNota->setPreceptor('');
            $oPersonaNota->setId_preceptor('');
        } else {
            $oPersonaNota->setPreceptor($Qpreceptor);
            $oPersonaNota->setId_preceptor($Qid_preceptor);
        }
        $oPersonaNota->setDetalle($Qdetalle);
        $oPersonaNota->setEpoca($Qepoca);
        $oPersonaNota->setId_activ($Qid_activ);
        $oPersonaNota->setNota_num($Qnota_num);
        $oPersonaNota->setNota_max($Qnota_max);

        if ($oPersonaNota->DBGuardar() === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
        break;
}


if (!empty($msg_err)) {
    echo $msg_err;
}	

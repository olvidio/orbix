<?php

use actividades\model\entity\Actividad;
use actividadestudios\model\entity\ActividadAsignaturaDl;
use actividadestudios\model\entity\GestorMatricula;
use actividadestudios\model\entity\Matricula;
use asignaturas\model\entity\Asignatura;
use core\ConfigGlobal;
use notas\model\entity\Acta;
use notas\model\entity\GestorActa;
use notas\model\entity\GestorPersonaNota;
use notas\model\entity\Nota;
use notas\model\entity\PersonaNota;
use personas\model\entity\Persona;
use web\TiposActividades;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)\filter_input(INPUT_POST, 'que');
$Qid_asignatura = (integer)\filter_input(INPUT_POST, 'id_asignatura');
$Qid_activ = (integer)\filter_input(INPUT_POST, 'id_activ');

$nota_corte = $_SESSION['oConfig']->getNota_corte();
$nota_max_default = $_SESSION['oConfig']->getNota_max();

if ($Qque == 3) { //paso las matrículas a notas definitivas (Grabar e imprimir)
    $aNivelOpcionales = array(1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434);
    $error = '';
    //$aIdSuperadas = $GesNotas->getArrayNotasSuperadas();
    // miro el acta
    $GesActas = new GestorActa();
    $cActas = $GesActas->getActas(array('id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura));
    // miro la epoca
    $oActividad = new Actividad($Qid_activ);
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $iepoca = PersonaNota::EPOCA_CA;
    $oTipoActividad = new TiposActividades($id_tipo_activ);
    $asistentes = $oTipoActividad->getAsistentesText();
    $actividad = $oTipoActividad->getActividadText();
    if ($asistentes == 'agd' && $actividad == 'ca') {
        $iepoca = PersonaNota::EPOCA_INVIERNO;
    }

    $GesMatriculas = new GestorMatricula();
    $cMatriculados = $GesMatriculas->getMatriculas(array('id_asignatura' => $Qid_asignatura, 'id_activ' => $Qid_activ));
    $i = 0;
    $msg_err = '';
    foreach ($cMatriculados as $oMatricula) {
        $i++;
        $aWhere = [];
        $aOperador = [];
        $id_nom = $oMatricula->getId_nom();
        // para saber a que schema pertenece la persona, utilizo el de la matrícula
        $id_schema = $oMatricula->getId_schema();
        $id_situacion = $oMatricula->getId_situacion();
        $preceptor = $oMatricula->getPreceptor();
        $nota_num = $oMatricula->getNota_num();
        $nota_max = $oMatricula->getNota_max();
        $acta = $oMatricula->getActa();

        if (empty($nota_max)) {
            $nota_max = $nota_max_default;
        }
        // Si es con precptor no se acepta cursado o examinado.
        if ($preceptor) {
            // Acepto nota_num=0 para borrar.
            if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
                $nn = $nota_num / $nota_max * 10;
                // Ahora si la guardo como examinado
                $oPersona = Persona::NewPersona($id_nom);
                if (!is_object($oPersona)) {
                    $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                    continue;
                }
                $error .= sprintf(_("nota no guardada para %s porque la nota (%s) no llega al mínimo: 6"), $oPersona->getNombreApellidos(), $nn) . "\n";
                continue;
            }
            if ($acta == Nota::CURSADA) {
                $error .= sprintf(_("no se puede definir cursada con preceptor") . "\n");
                exit($error);
            } else {
                $oActa = new Acta($acta);
                $f_acta = $oActa->getF_acta()->getFromLocal();
                if (empty($acta) || empty($f_acta)) {
                    $error .= sprintf(_("debe introducir los datos del acta. No se ha guardado nada.") . "\n");
                    exit($error);
                }
            }
        } else {
            // para las cursadas o examinadas no aprobadas
            if ($id_situacion == NOTA::CURSADA || $id_situacion == NOTA::EXAMINADO || empty($id_situacion)) {
                //conseguir una fecha para poner como fecha acta. las cursadas se guardan durante 2 años
                $f_acta = $cActas[0]->getF_acta()->getFromLocal();
            } else {
                if (empty($acta)) {
                    $error .= sprintf(_("falta definir el acta para alguna nota") . "\n");
                    exit($error);
                }
                $oActa = new Acta($acta);
                $f_acta = $oActa->getF_acta()->getFromLocal();
                if (empty($acta) || empty($f_acta)) {
                    $error .= sprintf(_("debe introducir los datos del acta. No se ha guardado nada.") . "\n");
                    exit($error);
                }
            }
        }
        // Acepto nota_num=0 para borrar.
        if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
            $nn = $nota_num / $nota_max * 10;
            $id_situacion = NOTA::EXAMINADO; // examinado
        }

        if (!empty($preceptor)) { //miro cuál
            $oActividadAsignatura = new ActividadAsignaturaDl(array('id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura));
            $id_preceptor = $oActividadAsignatura->getId_profesor();
        } else {
            $id_preceptor = '';
        }

        //Si es una opcional miro el id nivel para cada uno
        if ($Qid_asignatura > 3000) {
            switch (substr($Qid_asignatura, 1, 1)) {
                /* Ahora las opcionales son indiferentes a bienio/cuadrienio
                case 1:	// sólo de bienio
                    $aWhere['id_nivel'] = "123.";
                    $aOperador['id_nivel'] = '~';
                    $op_min=0;
                    $op_max=2;
                    break;
                case 2:	// sólo de caudrienio
                    $aWhere['id_nivel'] = "243.";
                    $aOperador['id_nivel'] = '~';
                    $op_min=3;
                    $op_max=7;
                    break;
                */
                default:
                    $aWhere['id_nivel'] = "^(12|24)3.";
                    $aOperador['id_nivel'] = '~';
                    $op_min = 0;
                    $op_max = 7;
            }
            $GesPersonaNotas = new GestorPersonaNota();
            $aWhere['id_nom'] = $id_nom;
            $aWhere['_ordre'] = 'id_nivel DESC';
            $cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhere, $aOperador);
            $id_op = '';
            $aOpSuperadas = array();
            $j = 0;
            $id_nivel = 0;
            foreach ($cPersonaNotas as $oPersonaNota1) {
                $j++;
                $id_op = $oPersonaNota1->getId_nivel();
                $id_asignatura = $oPersonaNota1->getId_asignatura();
                if ($id_asignatura == $Qid_asignatura) { // ya está la que intento meter => actualizar
                    $id_nivel = $id_op;
                    break;
                }
                // compruebo que corresponde a 'superada'
                $bAprobada = $oPersonaNota1->isAprobada();
                if (is_true($bAprobada)) {
                    $aOpSuperadas[$j] = $id_op;
                }
            }
            if (empty($id_nivel)) {
                for ($op = $op_min; $op <= $op_max; $op++) {
                    $id_nivel = $aNivelOpcionales[$op];
                    if (!in_array($id_nivel, $aOpSuperadas)) break;
                }
            }
            if ($id_nivel > $aNivelOpcionales[$op_max]) {
                $error .= sprintf(_("ha cursado una opcional que no tocaba (id_nom=%s)") . "\n", $id_nom);
                continue;
            }
        } else {
            $oAsignatura = new Asignatura($Qid_asignatura);
            $id_nivel = $oAsignatura->getId_nivel();
        }

        //compruebo que no existe ya la nota:
        //	- si existe y es en mismo id_activ, actualizo
        //  - si existe en otro id_activ, AVISO!!
        //
        $id_activ_old = 0;
        $oGesPersonaNota = new GestorPersonaNota();
        $cBuscarPersonaNotas = $oGesPersonaNota->getPersonaNotas(array('id_nom' => $id_nom, 'id_asignatura' => $Qid_asignatura));
        unset($oPersonaNotaAnterior);
        if (!empty($cBuscarPersonaNotas)) {
            $oPersonaNotaAnterior = $cBuscarPersonaNotas[0];
            $id_activ_old = $oPersonaNotaAnterior->getId_activ();
        }

        if (!empty($id_activ_old) && ($Qid_activ != $id_activ_old)) {
            //aviso
            $error .= sprintf(_("está intentando poner una nota que ya existe (id_nom=%s)") . "\n", $id_nom);
            continue;
        } else {
            switch ($acta) {
                case '':
                    if (isset($oPersonaNotaAnterior)) {
                        $oPersonaNotaAnterior->DBEliminar();
                    }
                    continue 2;
                    break;
                case Nota::CURSADA:
                    $id_situacion = NOTA::CURSADA;
                    break;
                default:
                    if (empty($id_situacion)) {
                        if (!empty($nota_num)) {
                            if ($nota_num / $nota_max < $nota_corte) {
                                $id_situacion = NOTA::EXAMINADO;
                            } else {
                                $id_situacion = NOTA::NUMERICA;
                            }
                        } else {
                            if (isset($oPersonaNotaAnterior)) {
                                $oPersonaNotaAnterior->DBEliminar();
                            }
                            continue 2;
                        }
                    }
            }

            $oPersonaNota = new PersonaNota(array('id_nom' => $id_nom, 'id_asignatura' => $Qid_asignatura));
            // guardo los datos
            $oPersonaNota->setId_schema($id_schema);
            $oPersonaNota->setId_nivel($id_nivel);
            $oPersonaNota->setId_situacion($id_situacion);
            $oPersonaNota->setActa($acta);
            $oPersonaNota->setF_acta($f_acta);
            $oPersonaNota->setId_activ($Qid_activ);
            $oPersonaNota->setPreceptor($preceptor);
            $oPersonaNota->setId_preceptor($id_preceptor);
            $oPersonaNota->setEpoca($iepoca);
            $oPersonaNota->setNota_num($nota_num);
            $oPersonaNota->setNota_max($nota_max);
            $oPersonaNota->setTipo_acta(PersonaNota::FORMATO_ACTA);
            if ($oPersonaNota->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oPersonaNota->getErrorTxt();
            }
        }
    }
    $go_to = ConfigGlobal::getWeb() . "/apps/notas/controller/acta_imprimir.php?acta=$acta|main";
}

if ($Qque == 1) { // Grabar las notas en la matricula
    $Qform_preceptor = (array)\filter_input(INPUT_POST, 'form_preceptor', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qid_nom = (array)\filter_input(INPUT_POST, 'id_nom', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qnota_num = (array)\filter_input(INPUT_POST, 'nota_num', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qnota_max = (array)\filter_input(INPUT_POST, 'nota_max', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qacta = (array)\filter_input(INPUT_POST, 'acta_nota', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    $num_alumnos = count($Qid_nom);
    $num_alumnos = empty($num_alumnos) ? 0 : $num_alumnos;

    for ($n = 0; $n < $num_alumnos; $n++) {
        if (!empty($Qform_preceptor[$n]) && $Qform_preceptor[$n] == "p") {
            $preceptor = "t";
        } else {
            $preceptor = "f";
        }
        $oMatricula = new Matricula(array('id_asignatura' => $Qid_asignatura, 'id_activ' => $Qid_activ, 'id_nom' => $Qid_nom[$n]));
        $oMatricula->setPreceptor($preceptor);
        // admitir coma y punto como separador decimal
        $nn = str_replace(',', '.', $Qnota_num[$n]);
        // ERROR
        if (!empty($Qnota_num[$n]) && $Qnota_num[$n] / $Qnota_max[$n] > 1) {
            $error = sprintf(_("Hay una nota mayor que el máximo") . "\n");
            exit($error);
        }
        $oMatricula->setNota_num($nn);
        $oMatricula->setNota_max($Qnota_max[$n]);
        $oMatricula->setActa($Qacta[$n]);
        // cursada o examinada para el caso sin preceptor
        if ($preceptor == 'f') {
            if ($Qacta[$n] == 2) {
                $oMatricula->setId_situacion(2);
                // examinada
                if ($Qnota_num[$n] > 1) $oMatricula->setId_situacion(12);
            } elseif ($Qnota_num[$n] > 1) {
                if (!empty($Qnota_num[$n]) && $Qnota_num[$n] / $Qnota_max[$n] < $nota_corte) {
                    // examinado
                    $oMatricula->setId_situacion(12);
                } else {
                    // aprobada
                    $oMatricula->setId_situacion(10);
                }
            }
        } else {
            if ($Qacta[$n] == Nota::CURSADA && $preceptor == true) {
                $error = sprintf(_("no se puede definir cursada con preceptor") . "\n");
                exit($error);
            }
            if (empty($Qnota_num[$n])) {
                $oMatricula->setId_situacion(0);
            } else {
                $oMatricula->setId_situacion(10);
            }
        }
        if ($oMatricula->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oMatricula->getErrorTxt();
        }
    }
    $go_to = '';
}

if (!empty($msg_err)) {
    echo $msg_err;
}
//vuelve a la presentacion de la ficha.
if (empty($error)) {
    if (!empty($go_to)) {
        $go_to = urlencode($go_to);
        //echo "gou: $go_to<br>";
//		echo $oPosicion->ir_a($go_to);
    }
} else {
    echo $error;
    echo "\n";

//	$go_avant = web\Hash::link(core\ConfigGlobal::getWeb().'/apps/notas/controller/acta_imprimir.php?'.http_build_query(array('acta'=>$acta)));
//	echo "<input type='button' onclick=fnjs_update_div('#main','".$go_avant."') value="._("continuar").">";

}
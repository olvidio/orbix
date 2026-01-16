<?php

use notas\model\EditarPersonaNota;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use web\TiposActividades;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (integer)filter_input(INPUT_POST, 'que');
$Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

$nota_corte = $_SESSION['oConfig']->getNotaCorte();
$nota_max_default = $_SESSION['oConfig']->getNotaMax();

$MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
if ($Qque === 3) { //paso las matrículas a notas definitivas (Grabar e imprimir)
    $aNivelOpcionales = array(1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434);
    $error = '';
    // miro el acta
    $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
    $cActas = $ActaRepository->getActas(array('id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura));
    // miro la epoca
    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadAllRepository->findById($Qid_activ);
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $iepoca = NotaEpoca::EPOCA_CA;
    $oTipoActividad = new TiposActividades($id_tipo_activ);
    $asistentes = $oTipoActividad->getAsistentesText();
    $actividad = $oTipoActividad->getActividadText();
    if ($asistentes === 'agd' && $actividad === 'ca') {
        $iepoca = NotaEpoca::EPOCA_INVIERNO;
    }

    $cMatriculados = $MatriculaRepository->getMatriculas(array('id_asignatura' => $Qid_asignatura, 'id_activ' => $Qid_activ));
    $i = 0;
    $msg_err = '';
    $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
    $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
    $ActividadASignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
    foreach ($cMatriculados as $oMatricula) {
        $i++;
        $aWhere = [];
        $aOperador = [];
        $id_nom = $oMatricula->getId_nom();
        // para saber a que schema pertenece la persona, utilizo el de la matrícula
        $id_schema = $oMatricula->getId_schema();
        $id_situacion = $oMatricula->getId_situacion();
        $preceptor = $oMatricula->isPreceptor();
        $nota_num = $oMatricula->getNota_num();
        $nota_max = $oMatricula->getNota_max();
        $acta = $oMatricula->getActa();

        if (empty($nota_max)) {
            $nota_max = $nota_max_default;
        }
        // Si es con preceptor no se acepta cursado o examinado.
        if ($preceptor) {
            // Acepto nota_num=0 para borrar.
            if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
                $nn = $nota_num / $nota_max * 10;
                // Ahora si la guardo como examinado
                $oPersona = Persona::findPersonaEnGlobal($id_nom);
                if ($oPersona === null) {
                    $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                    continue;
                }
                $error .= sprintf(_("nota no guardada para %s porque la nota (%s) no llega al mínimo: 6"), $oPersona->getNombreApellidos(), $nn) . "\n";
                continue;
            }
            if ((int)$acta === NotaSituacion::CURSADA) {
                $error .= _("no se puede definir cursada con preceptor") . "\n";
                exit($error);
            }

            $oActa = $ActaRepository->findById($acta);
            $oF_acta = $oActa->getF_acta();
            if (empty($acta) || empty($oF_acta)) {
                $error .= _("debe introducir los datos del acta. No se ha guardado nada.") . "\n";
                exit($error);
            }
        } else {
            // para las cursadas o examinadas no aprobadas
            if ($id_situacion === NotaSituacion::CURSADA || $id_situacion === NotaSituacion::EXAMINADO || empty($id_situacion)) {
                //conseguir una fecha para poner como fecha acta. las cursadas se guardan durante 2 años
                $oF_acta = $cActas[0]->getF_acta();
            } else {
                if (empty($acta)) {
                    $error .= _("falta definir el acta para alguna nota") . "\n";
                    exit($error);
                }
                $oActa = $ActaRepository->findById($acta);
                $oF_acta = $oActa->getF_acta();
                if (empty($oF_acta)) {
                    $error .= _("debe introducir los datos del acta. No se ha guardado nada.") . "\n";
                    exit($error);
                }
            }
        }
        // Acepto nota_num=0 para borrar.
        if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
            $nn = $nota_num / $nota_max * 10;
            $id_situacion = NotaSituacion::EXAMINADO; // examinado
        }

        if ($preceptor) { //miro cuál
            $oActividadAsignatura = $ActividadASignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
            $id_preceptor = $oActividadAsignatura->getId_profesor();
        } else {
            $id_preceptor = null;
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
                case 2:	// sólo de cuadrienio
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
            $aWhere['id_nom'] = $id_nom;
            $aWhere['_ordre'] = 'id_nivel DESC';
            $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
            $id_op = '';
            $aOpSuperadas = [];
            $j = 0;
            $id_nivel = 0;
            foreach ($cPersonaNotas as $oPersonaNota1) {
                $j++;
                $id_op = $oPersonaNota1->getId_nivel();
                $id_asignatura = $oPersonaNota1->getId_asignatura();
                if ($id_asignatura === $Qid_asignatura) { // ya está la que intento meter => actualizar
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
                    if (!in_array($id_nivel, $aOpSuperadas, true)) {
                        break;
                    }
                }
            }
            if ($id_nivel > $aNivelOpcionales[$op_max]) {
                $error .= sprintf(_("ha cursado una opcional que no tocaba (id_nom=%s)") . "\n", $id_nom);
                continue;
            }
        } else {
            $oAsignatura = $AsignaturaRepository->findById($Qid_asignatura);
            $id_nivel = $oAsignatura->getId_nivel();
        }

        //compruebo que no existe ya la nota:
        //	- si existe y es en mismo id_activ, actualizo
        //  - si existe en otro id_activ, AVISO!!
        //
        $id_activ_old = 0;
        $cBuscarPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas(array('id_nom' => $id_nom, 'id_asignatura' => $Qid_asignatura));
        unset($oPersonaNotaAnterior);
        if (!empty($cBuscarPersonaNotas)) {
            $oPersonaNotaAnterior = $cBuscarPersonaNotas[0];
            $id_activ_old = $oPersonaNotaAnterior->getId_activ();
        }

        if (!empty($id_activ_old) && ($Qid_activ !== $id_activ_old)) {
            //aviso
            $oAlumno = Persona::findPersonaEnGlobal($id_nom);
            if ($oAlumno === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            } else {
                $apellidos_nombre = $oAlumno->getApellidosNombre();
                $dl_persona = $oAlumno->getDl();
                $apellidos_nombre_dl = "$apellidos_nombre ($dl_persona)";
            }
            $error .= sprintf(_("está intentando poner una nota que ya existe para: %s") . "\n", $apellidos_nombre_dl);
        } else {
            switch ($acta) {
                case '':
                    if (isset($oPersonaNotaAnterior)) {
                        $oPersonaNotaAnterior->DBEliminar();
                    }
                    continue 2;
                case NotaSituacion::CURSADA:
                    $id_situacion = NotaSituacion::CURSADA;
                    break;
                default:
                    if (empty($id_situacion)) {
                        if (!empty($nota_num)) {
                            if ($nota_num / $nota_max < $nota_corte) {
                                $id_situacion = NotaSituacion::EXAMINADO;
                            } else {
                                $id_situacion = NotaSituacion::NUMERICA;
                            }
                        } else {
                            if (isset($oPersonaNotaAnterior)) {
                                $oPersonaNotaAnterior->DBEliminar();
                            }
                            continue 2;
                        }
                    }
            }


            $oPersonaNota = new PersonaNota();
            $oPersonaNota->setIdNivel($id_nivel);
            $oPersonaNota->setIdAsignatura($Qid_asignatura);
            $oPersonaNota->setIdNom($id_nom);
            $oPersonaNota->setIdSituacion($id_situacion);
            $oPersonaNota->setActa($acta);
            $oPersonaNota->setFActa($oF_acta);
            $oPersonaNota->setDetalle('');
            $oPersonaNota->setTipoActa(TipoActa::FORMATO_ACTA);
            $oPersonaNota->setPreceptor($preceptor);
            $oPersonaNota->setIdPreceptor($id_preceptor);
            $oPersonaNota->setEpoca($iepoca);
            $oPersonaNota->setIdActiv($Qid_activ);
            $oPersonaNota->setNotaNum($nota_num);
            $oPersonaNota->setNotaMax($nota_max);

            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);

            try {
                if (isset($oPersonaNotaAnterior)) {
                    $oEditarPersonaNota->editar($Qid_asignatura);
                } else {
                    $oEditarPersonaNota->nuevo();
                }
            } catch (\RuntimeException $e) {
                $msg_err .= "\r\n";
                $msg_err .= $e->getMessage();
            }
        }
    }
}

if ($Qque === 1) { // Grabar las notas en la matricula
    $Qform_preceptor = (array)filter_input(INPUT_POST, 'form_preceptor', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qid_nom = (array)filter_input(INPUT_POST, 'id_nom', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qnota_num = (array)filter_input(INPUT_POST, 'nota_num', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qnota_max = (array)filter_input(INPUT_POST, 'nota_max', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qacta = (array)filter_input(INPUT_POST, 'acta_nota', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    $num_alumnos = count($Qid_nom);
    $num_alumnos = empty($num_alumnos) ? 0 : $num_alumnos;

    for ($n = 0; $n < $num_alumnos; $n++) {
        if (!empty($Qform_preceptor[$n]) && $Qform_preceptor[$n] === "p") {
            $preceptor = TRUE;
        } else {
            $preceptor = FALSE;
        }
        $oMatricula = $MatriculaRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom[$n]);
        $oMatricula->setPreceptor($preceptor);
        // admitir coma y punto como separador decimal
        $nn = str_replace(',', '.', $Qnota_num[$n]);
        // ERROR
        if (!empty($Qnota_num[$n]) && $Qnota_num[$n] / $Qnota_max[$n] > 1) {
            $error = _("Hay una nota mayor que el máximo") . "\n";
            exit($error);
        }
        $oMatricula->setNota_num($nn);
        $oMatricula->setNota_max($Qnota_max[$n]);
        $oMatricula->setActa($Qacta[$n]);
        // cursada o examinada para el caso sin preceptor
        if ($preceptor === FALSE) {
            if ((int)$Qacta[$n] === 2) {
                $oMatricula->setId_situacion(2);
                // examinada
                if ($Qnota_num[$n] > 1) {
                    $oMatricula->setId_situacion(12);
                }
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
            if ((int)$Qacta[$n] === NotaSituacion::CURSADA && $preceptor === TRUE) {
                $error = _("no se puede definir cursada con preceptor") . "\n";
                exit($error);
            }
            if (empty($Qnota_num[$n])) {
                $oMatricula->setId_situacion(0);
            } else {
                $oMatricula->setId_situacion(10);
            }
        }
        if ($MatriculaRepository->Guardar($oMatricula) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oMatricula->getErrorTxt();
        }
    }
    $go_to = '';
}

if (!empty($msg_err)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $msg_err;
} else {
    $jsondata['success'] = TRUE;
}

//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata, JSON_THROW_ON_ERROR);
exit();

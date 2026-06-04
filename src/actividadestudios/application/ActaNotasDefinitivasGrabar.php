<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\EditarPersonaNota;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\is_true;

/**
 * Convierte las matriculas/notas borrador en notas definitivas (`PersonaNota`),
 * asignando epoca, id_nivel y creando/actualizando los registros oportunos.
 *
 * Sustituye a la rama `que=3` del legacy
 * `apps/actividadestudios/controller/acta_notas_update.php`.
 *
 * Devuelve array asociativo `['success' => bool, 'mensaje' => string]`. La
 * respuesta JSON la publica el controlador HTTP.
 */
final class ActaNotasDefinitivasGrabar
{
    public static function execute(array $input): array
    {
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $Qid_activ = (int) ($input['id_activ'] ?? 0);

        $nota_corte = $_SESSION['oConfig']->getNotaCorte();
        $nota_max_default = $_SESSION['oConfig']->getNotaMax();

        // plan97
        //$aNivelOpcionales = [1230, 1231, 1232, 2430, 2431, 2432, 2433, 2434];
        // actual
        $aNivelOpcionales = [1230, 1231, 2430, 2431, 2432];
        $error = '';
        $msg_err = '';

        $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $cActas = $ActaRepository->getActas(['id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura]);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($Qid_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $iepoca = NotaEpoca::EPOCA_CA;
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        if ($oTipoActividad->getAsistentesText() === 'agd' && $oTipoActividad->getActividadText() === 'ca') {
            $iepoca = NotaEpoca::EPOCA_INVIERNO;
        }

        $cMatriculados = $MatriculaRepository->getMatriculas(['id_asignatura' => $Qid_asignatura, 'id_activ' => $Qid_activ]);
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $ActividadASignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);

        foreach ($cMatriculados as $oMatricula) {
            $id_nom = $oMatricula->getId_nom();
            $id_situacion = $oMatricula->getId_situacion();
            $preceptor = $oMatricula->isPreceptor();
            $nota_num = $oMatricula->getNota_num();
            $nota_max = $oMatricula->getNota_max();
            $acta = $oMatricula->getActa();

            if (empty($nota_max)) {
                $nota_max = $nota_max_default;
            }

            if ($preceptor) {
                if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
                    $nn = $nota_num / $nota_max * 10;
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    if ($oPersona === null) {
                        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom";
                        continue;
                    }
                    $error .= sprintf(_('nota no guardada para %s porque la nota (%s) no llega al mínimo: 6'), $oPersona->getNombreApellidos(), $nn) . "\n";
                    continue;
                }
                if ((int) $acta === NotaSituacion::CURSADA) {
                    return ['success' => false, 'mensaje' => _('no se puede definir cursada con preceptor')];
                }

                $oActa = $ActaRepository->findById($acta);
                $oF_acta = $oActa->getF_acta();
                if (empty($acta) || empty($oF_acta)) {
                    return ['success' => false, 'mensaje' => _('debe introducir los datos del acta. No se ha guardado nada.')];
                }
            } else {
                if ($id_situacion === NotaSituacion::CURSADA || $id_situacion === NotaSituacion::EXAMINADO || empty($id_situacion)) {
                    $oF_acta = $cActas[0]->getF_acta();
                } else {
                    if (empty($acta)) {
                        return ['success' => false, 'mensaje' => _('falta definir el acta para alguna nota')];
                    }
                    $oActa = $ActaRepository->findById($acta);
                    $oF_acta = $oActa->getF_acta();
                    if (empty($oF_acta)) {
                        return ['success' => false, 'mensaje' => _('debe introducir los datos del acta. No se ha guardado nada.')];
                    }
                }
            }

            if (!empty($nota_num) && $nota_num / $nota_max < $nota_corte) {
                $id_situacion = NotaSituacion::EXAMINADO;
            }

            if ($preceptor) {
                $oActividadAsignatura = $ActividadASignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
                $id_preceptor = $oActividadAsignatura->getId_profesor();
            } else {
                $id_preceptor = null;
            }

            if ($Qid_asignatura > 3000) {
                $aWhere = ['id_nivel' => '^(12|24)3.', '_ordre' => 'id_nivel DESC'];
                $aOperador = ['id_nivel' => '~'];
                $op_min = 0;
                $op_max = 7;
                $aWhere['id_nom'] = $id_nom;
                $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
                $aOpSuperadas = [];
                $j = 0;
                $id_nivel = 0;
                foreach ($cPersonaNotas as $oPersonaNota1) {
                    $j++;
                    $id_op = $oPersonaNota1->getIdNivelVo()->value();
                    $id_asignatura_tmp = $oPersonaNota1->getId_asignatura();
                    if ($id_asignatura_tmp === $Qid_asignatura) {
                        $id_nivel = $id_op;
                        break;
                    }
                    if (is_true($oPersonaNota1->isAprobada())) {
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
                    $error .= sprintf(_('ha cursado una opcional que no tocaba (id_nom=%s)') . "\n", $id_nom);
                    continue;
                }
            } else {
                $oAsignatura = $AsignaturaRepository->findById($Qid_asignatura);
                $id_nivel = $oAsignatura->getIdNivelVo()->value();
            }

            $cBuscarPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $Qid_asignatura]);
            $oPersonaNotaAnterior = null;
            $id_activ_old = 0;
            if (!empty($cBuscarPersonaNotas)) {
                $oPersonaNotaAnterior = $cBuscarPersonaNotas[0];
                $id_activ_old = $oPersonaNotaAnterior->getId_activ();
            }

            if (!empty($id_activ_old) && ($Qid_activ !== $id_activ_old)) {
                $oAlumno = Persona::findPersonaEnGlobal($id_nom);
                $apellidos_nombre_dl = ($oAlumno === null)
                    ? "id_nom=$id_nom"
                    : $oAlumno->getApellidosNombre() . ' (' . $oAlumno->getDl() . ')';
                $error .= sprintf(_('está intentando poner una nota que ya existe para: %s') . "\n", $apellidos_nombre_dl);
                continue;
            }

            switch ($acta) {
                case '':
                    if ($oPersonaNotaAnterior !== null) {
                        $oPersonaNotaAnterior->DBEliminar();
                    }
                    continue 2;
                case NotaSituacion::CURSADA:
                    $id_situacion = NotaSituacion::CURSADA;
                    break;
                default:
                    if (empty($id_situacion)) {
                        if (!empty($nota_num)) {
                            $id_situacion = ($nota_num / $nota_max < $nota_corte)
                                ? NotaSituacion::EXAMINADO
                                : NotaSituacion::NUMERICA;
                        } else {
                            if ($oPersonaNotaAnterior !== null) {
                                $oPersonaNotaAnterior->DBEliminar();
                            }
                            continue 2;
                        }
                    }
            }

            $oPersonaNota = new PersonaNota();
            $oPersonaNota->setIdNivelVo(NivelId::fromNullableInt($id_nivel));
            $oPersonaNota->setIdAsignaturaVo($Qid_asignatura);
            $oPersonaNota->setId_nom($id_nom);
            $oPersonaNota->setIdSituacionVo($id_situacion);
            $oPersonaNota->setActaVo($acta);
            $oPersonaNota->setF_acta($oF_acta);
            $oPersonaNota->setDetalleVo('');
            $oPersonaNota->setTipoActaVo(TipoActa::FORMATO_ACTA);
            $oPersonaNota->setPreceptor($preceptor);
            $oPersonaNota->setId_preceptor($id_preceptor);
            $oPersonaNota->setEpocaVo($iepoca);
            $oPersonaNota->setId_activ($Qid_activ);
            $oPersonaNota->setNotaNumVo($nota_num);
            $oPersonaNota->setNotaMaxVo($nota_max);

            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            try {
                if ($oPersonaNotaAnterior !== null) {
                    $oEditarPersonaNota->editar($Qid_asignatura);
                } else {
                    $oEditarPersonaNota->nuevo();
                }
            } catch (\RuntimeException $e) {
                $msg_err .= "\r\n" . $e->getMessage();
            }
        }

        if (!empty($error)) {
            $msg_err = trim($msg_err . "\n" . $error);
        }
        return [
            'success' => empty($msg_err),
            'mensaje' => $msg_err,
        ];
    }
}

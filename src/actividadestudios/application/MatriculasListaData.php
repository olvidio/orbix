<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Listado de matrículas en un intervalo de fechas (actividades cuyo `f_ini`
 * cae en el periodo). Usado por `matriculas_lista` vía PostRequest.
 */
final class MatriculasListaData
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private TelecoPersonaService $telecoPersonaService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   titulo: string,
     *   msg_err: string,
     *   a_valores: array<int|string, array<string|int, mixed>>
     * }
     */
    public function execute(array $input): array
    {
        $inicioIso = FuncTablasSupport::inputString($input, 'inicio_iso');
        $finIso = FuncTablasSupport::inputString($input, 'fin_iso');
        $aWhereActividad = [
            'f_ini' => "'$inicioIso','$finIso'",
        ];
        $aOperadorActividad = ['f_ini' => 'BETWEEN'];

        $aIdActividades = $this->actividadRepository->getArrayIdsWithKeyFini(
            $aWhereActividad,
            $aOperadorActividad,
        );

        $strActividades = '{' . implode(', ', $aIdActividades) . '}';
        $aWhere = ['id_activ' => $strActividades];
        $aOperador = ['id_activ' => 'ANY'];

        $cMatriculas = $this->matriculaDlRepository->getMatriculas($aWhere, $aOperador);

        $oFini = new DateTimeLocal($inicioIso);
        $QinicioLocal = $oFini->getFromLocal();
        $oFfin = new DateTimeLocal($finIso);
        $QfinLocal = $oFfin->getFromLocal();
        $titulo = _(sprintf(
            _('Lista de matrículas en el periodo: %s - %s.'),
            $QinicioLocal,
            $QfinLocal,
        ));

        $i = 0;
        $aValores = [];
        $msgErr = '';
        $idNomAnterior = '';
        $aNombre = [];
        $aAsignatura = [];
        $apellidosNombre = '';
        $ctr = '';
        $dl = '';

        foreach ($cMatriculas as $oMatricula) {
            $i++;
            $idNom = $oMatricula->getId_nom();
            $idActiv = $oMatricula->getId_activ();
            $idAsignatura = $oMatricula->getId_asignatura();
            $notaTxt = '';
            if ($oMatricula->getNotaNumVo() !== null) {
                $notaMax = $oMatricula->getNotaMaxVo();
                $notaTxt = $oMatricula->getNotaNumVo()->value() . ' ['
                    . ($notaMax !== null ? $notaMax->value() : '') . ']';
            }
            $preceptor = $oMatricula->isPreceptor();
            if (FuncTablasSupport::isTrue($preceptor)) {
                $preceptor = 'x';
                $idPreceptor = $oMatricula->getId_preceptor();
                $mailsPreceptor = '';
                if (!empty($idPreceptor)) {
                    $oPersona = Persona::findPersonaEnGlobal($idPreceptor);
                    if ($oPersona === null) {
                        $msgErr .= "<br>preceptor: No encuentro a nadie con id_nom: $idPreceptor en  "
                            . __FILE__ . ': line ' . __LINE__;
                    } else {
                        $preceptor = $oPersona->getPrefApellidosNombre();
                        $mailsPreceptor = $this->telecoPersonaService->getTelecosPorTipo(
                            $idPreceptor,
                            'e-mail',
                            ' / ',
                        );
                        if (!empty($mailsPreceptor)) {
                            $preceptor .= ' [' . $mailsPreceptor . ']';
                        }
                    }
                }
            } else {
                $preceptor = '';
            }

            $oActividad = $this->actividadAllRepository->findById($idActiv);
            if ($oActividad === null) {
                $msgErr .= "<br>" . sprintf(_('No encuentro actividad con id: %d'), $idActiv);
                continue;
            }
            $nomActiv = $oActividad->getNom_activ();

            if ($idNom !== $idNomAnterior) {
                $oPersona = Persona::findPersonaEnGlobal($idNom);
                if ($oPersona === null) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  "
                        . __FILE__ . ': line ' . __LINE__;
                    continue;
                }
                $apellidosNombre = $oPersona->getPrefApellidosNombre();
                $ctr = $oPersona->getCentro_o_dl();
                $dl = $oPersona->getDl();
                $mailsAlumno = $this->telecoPersonaService->getTelecosPorTipo($idNom, 'e-mail', ' / ');
                if (!empty($mailsAlumno)) {
                    $apellidosNombre .= ' [' . $mailsAlumno . ']';
                }
            }

            $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(
                    sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura),
                );
            }
            $nombreCorto = $oAsignatura->getNombre_corto();

            $aValores[$i]['sel'] = "$idActiv#$idAsignatura#$idNom";
            $aValores[$i][1] = $apellidosNombre;
            $aValores[$i][2] = $ctr;
            $aValores[$i][3] = $dl;
            $aValores[$i][4] = $nomActiv;
            $aValores[$i][5] = $nombreCorto;
            $aValores[$i][6] = $preceptor;
            $aValores[$i][7] = $notaTxt;

            $aNombre[$i] = $apellidosNombre;
            $aAsignatura[$i] = $nombreCorto;

            $idNomAnterior = $idNom;
        }

        if (!empty($aValores)) {
            array_multisort(
                $aNombre,
                SORT_STRING,
                $aAsignatura,
                SORT_STRING,
                $aValores,
            );
        }

        return [
            'titulo' => $titulo,
            'msg_err' => $msgErr,
            'a_valores' => $aValores,
        ];
    }
}

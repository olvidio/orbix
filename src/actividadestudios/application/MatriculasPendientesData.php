<?php

namespace src\actividadestudios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use function frontend\shared\helpers\is_true;

/**
 * Filas para `frontend/actividadestudios/controller/matriculas_pendientes.php`.
 *
 * @return array{msg_err: string, a_valores: array<int|string, array<string|int, mixed>>}
 */
final class MatriculasPendientesData
{
    public function __construct(
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{msg_err: string, a_valores: array<int|string, array<string|int, mixed>>}
     */
    public function execute(array $input = []): array
    {
        $cMatriculasPendientes = $this->matriculaDlRepository->getMatriculasPendientes();

        $msgErr = '';
        $i = 0;
        $aValores = [];
        foreach ($cMatriculasPendientes as $oMatricula) {
            $i++;
            $idNom = $oMatricula->getId_nom();
            $idActiv = $oMatricula->getId_activ();
            $idAsignatura = $oMatricula->getId_asignatura();
            $preceptor = $oMatricula->isPreceptor();
            $preceptorTxt = is_true($preceptor) ? 'x' : '';

            $oActividad = $this->actividadAllRepository->findById($idActiv);
            if ($oActividad === null) {
                $msgErr .= "<br>No encuentro ninguna actividad con id: $idActiv en  " . __FILE__ . ': line ' . __LINE__;
                continue;
            }
            $nomActiv = $oActividad->getNom_activ();

            try {
                $oPersona = $this->personaFinderService->findPersonaEnGlobal($idNom);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf(
                    _('Error al validar nombre o apellidos de persona en matrículas pendientes (fila de lista %1$d): id_nom=%2$d, id_activ=%3$d, id_asignatura=%4$d. %5$s'),
                    $i,
                    $idNom,
                    $idActiv,
                    $idAsignatura,
                    $e->getMessage()
                ), 0, $e);
            }
            if ($oPersona === null) {
                $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
                continue;
            }
            $apellidosNombre = $oPersona->getPrefApellidosNombre();

            $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura));
            }
            $nombreCorto = $oAsignatura->getNombre_corto();

            $aValores[$i]['sel'] = "$idActiv#$idAsignatura#$idNom";
            $aValores[$i][1] = $nomActiv;
            $aValores[$i][2] = $nombreCorto;
            $aValores[$i][3] = $apellidosNombre;
            $aValores[$i][4] = $preceptorTxt;
        }

        return [
            'msg_err' => $msgErr,
            'a_valores' => $aValores,
        ];
    }
}

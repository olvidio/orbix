<?php

namespace src\actividadestudios\application;

use src\actividadestudios\application\support\ActividadAsignaturaSelToken;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\personas\application\services\PersonaFinderService;

/**
 * Elimina una `ActividadAsignatura` (asignatura impartida en un ca).
 *
 * Si hay alumnos matriculados pide confirmación (cuántos y de qué dl) y, al
 * aceptar, borra también esas matrículas de esta dl.
 *
 * Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaEliminar
{
    public function __construct(
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @param array<int|string, int> $porDl
     */
    public static function mensajeConMatriculas(int $total, array $porDl): string
    {
        $partes = [];
        foreach ($porDl as $dl => $n) {
            $partes[] = $dl . ': ' . $n;
        }
        $detalle = implode(', ', $partes);
        if ($total === 1) {
            return sprintf(
                _("Hay 1 alumno matriculado (%s). Si continúa se borrará también esa matrícula."),
                $detalle,
            );
        }

        return sprintf(
            _("Hay %s alumnos matriculados (%s). Si continúa se borrarán también esas matrículas."),
            (string) $total,
            $detalle,
        );
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string, requiere_confirmacion: bool, mensaje: string}
     */
    public function execute(array $input): array
    {
        $Qpau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'pau');
        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        $confirmar = \src\shared\domain\helpers\FuncTablasSupport::isTrue($input['confirmar_con_matriculas'] ?? false) === true;

        if (!empty($a_sel) && $Qpau === 'a') {
            $sel = $a_sel[0];
            $decoded = ActividadAsignaturaSelToken::decode(is_scalar($sel) ? (string) $sel : '');
            $Qid_activ = $decoded['id_activ'] > 0 ? $decoded['id_activ'] : $Qid_activ;
            $Qid_asignatura = $decoded['id_asignatura'] > 0 ? $decoded['id_asignatura'] : $Qid_asignatura;
        }

        if ($Qpau !== 'a') {
            return self::resultado(_("sólo se puede eliminar una asignatura desde el dossier de la actividad"));
        }
        if ($Qid_activ <= 0 || $Qid_asignatura <= 0) {
            return self::resultado(_("faltan claves de la asignatura de actividad"));
        }

        $oActividadAsignatura = $this->actividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        if ($oActividadAsignatura === null) {
            return self::resultado(_("no encuentro la asignatura"));
        }

        $matriculas = $this->matriculaDlRepository->getMatriculas([
            'id_activ' => $Qid_activ,
            'id_asignatura' => $Qid_asignatura,
        ]);
        if ($matriculas !== [] && !$confirmar) {
            $porDl = $this->contarPorDl($matriculas);

            return [
                'error' => '',
                'requiere_confirmacion' => true,
                'mensaje' => self::mensajeConMatriculas(count($matriculas), $porDl),
            ];
        }

        foreach ($matriculas as $oMatricula) {
            if ($this->matriculaDlRepository->Eliminar($oMatricula) === false) {
                return self::resultado(_("hay un error, no se ha borrado"));
            }
        }

        if ($this->actividadAsignaturaDlRepository->Eliminar($oActividadAsignatura) === false) {
            return self::resultado(_("hay un error, no se ha borrado"));
        }

        return self::resultado('');
    }

    /**
     * @param list<Matricula> $matriculas
     * @return array<string, int>
     */
    private function contarPorDl(array $matriculas): array
    {
        $porDl = [];
        foreach ($matriculas as $oMatricula) {
            $oPersona = $this->personaFinderService->findPersonaEnGlobalODePaso($oMatricula->getId_nom());
            $dl = '?';
            if ($oPersona !== null) {
                $dlPersona = $oPersona->getDl();
                if (is_string($dlPersona) && $dlPersona !== '') {
                    $dl = $dlPersona;
                }
            }
            $porDl[$dl] = ($porDl[$dl] ?? 0) + 1;
        }
        ksort($porDl);

        return $porDl;
    }

    /**
     * @return array{error: string, requiere_confirmacion: bool, mensaje: string}
     */
    private static function resultado(string $error): array
    {
        return [
            'error' => $error,
            'requiere_confirmacion' => false,
            'mensaje' => '',
        ];
    }
}

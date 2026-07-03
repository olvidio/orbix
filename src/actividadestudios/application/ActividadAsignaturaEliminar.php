<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Elimina una `ActividadAsignatura` (asignatura impartida en un ca).
 *
 * Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaEliminar
{
    public function __construct(
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qpau = FuncTablasSupport::inputString($input, 'pau');
        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_asignatura = FuncTablasSupport::inputInt($input, 'id_asignatura');

        if (!empty($a_sel) && $Qpau === 'a') {
            $sel = $a_sel[0];
            $Qid_activ = (int) strtok(is_scalar($sel) ? (string) $sel : '', '#');
            $Qid_asignatura = (int) strtok('#');
        }

        if ($Qpau !== 'a') {
            return _("sólo se puede eliminar una asignatura desde el dossier de la actividad");
        }
        if ($Qid_activ <= 0 || $Qid_asignatura <= 0) {
            return _("faltan claves de la asignatura de actividad");
        }

        $oActividadAsignatura = $this->actividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        if ($oActividadAsignatura === null) {
            return _("no encuentro la asignatura");
        }
        if ($this->actividadAsignaturaDlRepository->Eliminar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha borrado");
        }
        return '';
    }
}

<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;

/**
 * Elimina una `ActividadAsignatura` (asignatura impartida en un ca).
 *
 * Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaEliminar
{
    public static function execute(array $input): string
    {
        $Qpau = (string) ($input['pau'] ?? '');
        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);

        if (!empty($a_sel) && $Qpau === 'a') {
            $Qid_activ = (int) strtok($a_sel[0], '#');
            $Qid_asignatura = (int) strtok('#');
        }

        if ($Qpau !== 'a') {
            return _("sólo se puede eliminar una asignatura desde el dossier de la actividad");
        }
        if (empty($Qid_activ) || empty($Qid_asignatura)) {
            return _("faltan claves de la asignatura de actividad");
        }

        $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        if ($oActividadAsignatura === null) {
            return _("no encuentro la asignatura");
        }
        if ($ActividadAsignaturaDlRepository->Eliminar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha borrado");
        }
        return '';
    }
}

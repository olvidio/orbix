<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Edita una `ActividadAsignatura` existente.
 *
 * Sustituye al case `editar` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaEditar
{
    public static function execute(array $input): string
    {
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $Qid_profesor = (int) ($input['id_profesor'] ?? 0);
        $Qavis_profesor = (string) ($input['avis_profesor'] ?? '');
        $Qtipo = (string) ($input['tipo'] ?? '');
        $Qf_ini = (string) ($input['f_ini'] ?? '');
        $Qf_fin = (string) ($input['f_fin'] ?? '');

        if (empty($Qid_activ) || empty($Qid_asignatura)) {
            return _("faltan claves de la asignatura de actividad");
        }

        $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        if ($oActividadAsignatura === null) {
            return _("no encuentro la asignatura");
        }
        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $oActividadAsignatura->setF_ini(DateTimeLocal::createFromLocal($Qf_ini));
        $oActividadAsignatura->setF_fin(DateTimeLocal::createFromLocal($Qf_fin));
        if ($ActividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}

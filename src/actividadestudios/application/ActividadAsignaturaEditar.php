<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Edita una `ActividadAsignatura` existente.
 *
 * Sustituye al case `editar` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaEditar
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
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_asignatura = FuncTablasSupport::inputInt($input, 'id_asignatura');
        $Qid_profesor = FuncTablasSupport::inputInt($input, 'id_profesor');
        $Qavis_profesor = FuncTablasSupport::inputString($input, 'avis_profesor');
        $Qtipo = FuncTablasSupport::inputString($input, 'tipo');
        $Qf_ini = FuncTablasSupport::inputString($input, 'f_ini');
        $Qf_fin = FuncTablasSupport::inputString($input, 'f_fin');

        if ($Qid_activ <= 0 || $Qid_asignatura <= 0) {
            return _("faltan claves de la asignatura de actividad");
        }

        $oActividadAsignatura = $this->actividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        if ($oActividadAsignatura === null) {
            return _("no encuentro la asignatura");
        }
        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $rawF_ini = $Qf_ini === '' ? null : DateTimeLocal::createFromLocal($Qf_ini);
        $rawF_fin = $Qf_fin === '' ? null : DateTimeLocal::createFromLocal($Qf_fin);
        $oActividadAsignatura->setF_ini($rawF_ini instanceof DateTimeLocal ? $rawF_ini : null);
        $oActividadAsignatura->setF_fin($rawF_fin instanceof DateTimeLocal ? $rawF_fin : null);
        if ($this->actividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}

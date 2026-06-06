<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

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
        $Qid_activ = input_int($input, 'id_activ');
        $Qid_asignatura = input_int($input, 'id_asignatura');
        $Qid_profesor = input_int($input, 'id_profesor');
        $Qavis_profesor = input_string($input, 'avis_profesor');
        $Qtipo = input_string($input, 'tipo');
        $Qf_ini = input_string($input, 'f_ini');
        $Qf_fin = input_string($input, 'f_fin');

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
        $oActividadAsignatura->setF_ini(DateTimeLocal::createFromLocal($Qf_ini));
        $oActividadAsignatura->setF_fin(DateTimeLocal::createFromLocal($Qf_fin));
        if ($this->actividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}

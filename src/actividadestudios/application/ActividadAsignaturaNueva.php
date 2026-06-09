<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\domain\value_objects\DateTimeLocal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el
 * dossier 3005 de la actividad.
 *
 * Sustituye al case `nuevo` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaNueva
{
    public function __construct(
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private DossierRepositoryInterface $dossierRepository,
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

        $oActividadAsignatura = new ActividadAsignatura();
        $oActividadAsignatura->setId_activ($Qid_activ);
        $oActividadAsignatura->setId_asignatura($Qid_asignatura);
        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $rawF_ini = $Qf_ini === '' ? null : DateTimeLocal::createFromLocal($Qf_ini);
        $rawF_fin = $Qf_fin === '' ? null : DateTimeLocal::createFromLocal($Qf_fin);
        $oActividadAsignatura->setF_ini($rawF_ini instanceof DateTimeLocal ? $rawF_ini : null);
        $oActividadAsignatura->setF_fin($rawF_fin instanceof DateTimeLocal ? $rawF_fin : null);
        if ($this->actividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha creado");
        }

        $oDossier = $this->dossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3005,
        ]));
        if ($oDossier === null) {
            $oDossier = $this->dossierRepository->crearDossier(DossierPk::fromArray([
                'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3005,
            ]));
        }
        $oDossier->abrir();
        $this->dossierRepository->Guardar($oDossier);
        return '';
    }
}

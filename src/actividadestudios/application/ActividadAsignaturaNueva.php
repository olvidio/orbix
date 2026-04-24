<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Crea una `ActividadAsignatura` (asignatura impartida en un ca) y abre el
 * dossier 3005 de la actividad.
 *
 * Sustituye al case `nuevo` del antiguo `update_3005.php` dispatcher.
 */
final class ActividadAsignaturaNueva
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
        $oActividadAsignatura = new ActividadAsignatura();
        $oActividadAsignatura->setId_activ($Qid_activ);
        $oActividadAsignatura->setId_asignatura($Qid_asignatura);
        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $oActividadAsignatura->setF_ini(DateTimeLocal::createFromLocal($Qf_ini));
        $oActividadAsignatura->setF_fin(DateTimeLocal::createFromLocal($Qf_fin));
        if ($ActividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            return _("hay un error, no se ha creado");
        }

        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $oDossier = $DossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3005,
        ]));
        if ($oDossier === null) {
            $oDossier = $DossierRepository->crearDossier(DossierPk::fromArray([
                'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3005,
            ]));
        }
        $oDossier->abrir();
        $DossierRepository->Guardar($oDossier);
        return '';
    }
}

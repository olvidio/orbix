<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;

/**
 * Elimina un `TipoDossier`.
 *
 * Sustituye al case `eliminar` del antiguo
 * `apps/dossiers/controller/perm_dossier_update.php`.
 */
final class TipoDossierEliminar
{
    public static function execute(array $input): string
    {
        $Qid_tipo_dossier = (int) ($input['id_tipo_dossier'] ?? 0);
        if ($Qid_tipo_dossier <= 0) {
            return _("falta id_tipo_dossier");
        }

        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $oTipoDossier = $TipoDossierRepository->findById($Qid_tipo_dossier);
        if ($oTipoDossier === null) {
            return sprintf(_("No se encuentra el dossier: %s"), $Qid_tipo_dossier);
        }

        if ($TipoDossierRepository->Eliminar($oTipoDossier) === false) {
            return _("Hay un error, no se ha eliminado.");
        }
        return '';
    }
}

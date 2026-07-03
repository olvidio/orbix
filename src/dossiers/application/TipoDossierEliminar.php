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
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_tipo_dossier = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tipo_dossier');
        if ($Qid_tipo_dossier <= 0) {
            return _('falta id_tipo_dossier');
        }

        $oTipoDossier = $this->tipoDossierRepository->findById($Qid_tipo_dossier);
        if ($oTipoDossier === null) {
            return sprintf(_('No se encuentra el dossier: %s'), $Qid_tipo_dossier);
        }

        if ($this->tipoDossierRepository->Eliminar($oTipoDossier) === false) {
            return _('Hay un error, no se ha eliminado.');
        }
        return '';
    }
}

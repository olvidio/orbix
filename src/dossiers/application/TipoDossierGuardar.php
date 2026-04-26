<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Guarda los cambios a un `TipoDossier`.
 *
 * Sustituye al case `guardar` del antiguo
 * `apps/dossiers/controller/perm_dossier_update.php`.
 */
final class TipoDossierGuardar
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

        $Qdescripcion = (string) ($input['descripcion'] ?? '');
        $Qtabla_from = (string) ($input['tabla_from'] ?? '');
        $Qtabla_to = (string) ($input['tabla_to'] ?? '');
        $Qcampo_to = (string) ($input['campo_to'] ?? '');
        $Qid_tipo_dossier_rel = (int) ($input['id_tipo_dossier_rel'] ?? 0);
        $Qdepende_modificar = (string) ($input['depende_modificar'] ?? '');
        $Qapp = (string) ($input['app'] ?? '');
        $Qclass = (string) ($input['class'] ?? '');
        $Qcodigo = (string) ($input['codigo'] ?? '');
        $aPermiso_lectura = (array) ($input['Permiso_lectura'] ?? []);
        $aPermiso_escritura = (array) ($input['Permiso_escritura'] ?? []);

        $oTipoDossier->setDescripcion($Qdescripcion);
        $oTipoDossier->setTabla_from($Qtabla_from);
        $oTipoDossier->setTabla_to($Qtabla_to);
        $oTipoDossier->setCampo_to($Qcampo_to);
        $oTipoDossier->setId_tipo_dossier_rel($Qid_tipo_dossier_rel);
        $oTipoDossier->setDepende_modificar(is_true($Qdepende_modificar));
        $oTipoDossier->setApp($Qapp);
        $oTipoDossier->setClass($Qclass);
        $oTipoDossier->setCodigo(trim($Qcodigo) !== '' ? trim($Qcodigo) : null);
        $oTipoDossier->setDb(1);

        if (!empty($aPermiso_lectura) && count($aPermiso_lectura) > 0) {
            $byte = 0;
            foreach ($aPermiso_lectura as $bit) {
                $byte += (int) $bit;
            }
            $oTipoDossier->setPermiso_lectura($byte);
        }
        if (!empty($aPermiso_escritura) && count($aPermiso_escritura) > 0) {
            $byte = 0;
            foreach ($aPermiso_escritura as $bit) {
                $byte += (int) $bit;
            }
            $oTipoDossier->setPermiso_escritura($byte);
        }

        if ($TipoDossierRepository->Guardar($oTipoDossier) === false) {
            return _("Hay un error, no se ha guardado.");
        }
        return '';
    }
}

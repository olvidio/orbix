<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Guarda los cambios a un `TipoDossier`.
 *
 * Sustituye al case `guardar` del antiguo
 * `apps/dossiers/controller/perm_dossier_update.php`.
 */
final class TipoDossierGuardar
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
        $Qid_tipo_dossier = input_int($input, 'id_tipo_dossier');
        if ($Qid_tipo_dossier <= 0) {
            return _('falta id_tipo_dossier');
        }

        $oTipoDossier = $this->tipoDossierRepository->findById($Qid_tipo_dossier);
        if ($oTipoDossier === null) {
            return sprintf(_('No se encuentra el dossier: %s'), $Qid_tipo_dossier);
        }

        $Qdescripcion = input_string($input, 'descripcion');
        $Qtabla_from = input_string($input, 'tabla_from');
        $Qtabla_to = input_string($input, 'tabla_to');
        $Qcampo_to = input_string($input, 'campo_to');
        $Qid_tipo_dossier_rel = input_int($input, 'id_tipo_dossier_rel');
        $Qdepende_modificar = input_string($input, 'depende_modificar');
        $Qapp = input_string($input, 'app');
        $Qclass = input_string($input, 'class');
        $Qcodigo = input_string($input, 'codigo');
        $aPermiso_lectura = isset($input['Permiso_lectura']) && is_array($input['Permiso_lectura'])
            ? $input['Permiso_lectura']
            : [];
        $aPermiso_escritura = isset($input['Permiso_escritura']) && is_array($input['Permiso_escritura'])
            ? $input['Permiso_escritura']
            : [];

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

        if ($aPermiso_lectura !== []) {
            $byte = 0;
            foreach ($aPermiso_lectura as $bit) {
                $byte += is_numeric($bit) ? (int) $bit : 0;
            }
            $oTipoDossier->setPermiso_lectura($byte);
        }
        if ($aPermiso_escritura !== []) {
            $byte = 0;
            foreach ($aPermiso_escritura as $bit) {
                $byte += is_numeric($bit) ? (int) $bit : 0;
            }
            $oTipoDossier->setPermiso_escritura($byte);
        }

        if ($this->tipoDossierRepository->Guardar($oTipoDossier) === false) {
            return _('Hay un error, no se ha guardado.');
        }
        return '';
    }
}

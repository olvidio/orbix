<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qid_tipo_dossier = FuncTablasSupport::inputInt($input, 'id_tipo_dossier');
        if ($Qid_tipo_dossier <= 0) {
            return _('falta id_tipo_dossier');
        }

        $oTipoDossier = $this->tipoDossierRepository->findById($Qid_tipo_dossier);
        if ($oTipoDossier === null) {
            return sprintf(_('No se encuentra el dossier: %s'), $Qid_tipo_dossier);
        }

        $Qdescripcion = FuncTablasSupport::inputString($input, 'descripcion');
        $Qtabla_from = FuncTablasSupport::inputString($input, 'tabla_from');
        $Qtabla_to = FuncTablasSupport::inputString($input, 'tabla_to');
        $Qcampo_to = FuncTablasSupport::inputString($input, 'campo_to');
        $Qid_tipo_dossier_rel = FuncTablasSupport::inputInt($input, 'id_tipo_dossier_rel');
        $Qdepende_modificar = FuncTablasSupport::inputString($input, 'depende_modificar');
        $Qapp = FuncTablasSupport::inputString($input, 'app');
        $Qclass = FuncTablasSupport::inputString($input, 'class');
        $Qcodigo = FuncTablasSupport::inputString($input, 'codigo');
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
        $oTipoDossier->setDepende_modificar(FuncTablasSupport::isTrue($Qdepende_modificar) ?? false);
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

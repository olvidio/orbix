<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_string;

/**
 * Mutacion: crea o actualiza un `TipoTarifa`.
 */
final class TipoTarifaUpdate
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_tarifa = input_string($input, 'id_tarifa');
        $letra = input_string($input, 'letra');
        $modo = input_string($input, 'modo');
        $observ = input_string($input, 'observ');

        if ($id_tarifa === 'nuevo' || $id_tarifa === '') {
            $newId = $this->tipoTarifaRepository->getNewId();
            $oTipoTarifa = new TipoTarifa();
            $oTipoTarifa->setId_tarifa($newId);
            $oTipoTarifa->setSfsv(ConfigGlobal::mi_sfsv());
        } else {
            $oTipoTarifa = $this->tipoTarifaRepository->findById((int) $id_tarifa);
            if ($oTipoTarifa === null) {
                return (string) _("no se encuentra la tarifa");
            }
        }

        if ($letra !== '') {
            $oTipoTarifa->setLetra($letra);
        }
        if ($modo !== '') {
            $oTipoTarifa->setModo((int) $modo);
        }
        if ($observ !== '') {
            $oTipoTarifa->setObserv($observ);
        }

        if ($this->tipoTarifaRepository->Guardar($oTipoTarifa) === false) {
            return (string) _("hay un error, no se ha guardado")
                . "\n" . $this->tipoTarifaRepository->getErrorTxt();
        }

        return '';
    }
}

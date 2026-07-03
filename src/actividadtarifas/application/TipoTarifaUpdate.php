<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\shared\config\ConfigGlobal;

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
        $id_tarifa = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tarifa');
        $letra = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'letra');
        $modo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'modo');
        $observ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ');

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

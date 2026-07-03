<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

/**
 * Mutacion: elimina un `TipoTarifa`.
 */
final class TipoTarifaEliminar
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
        $id_tarifa = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tarifa');
        if ($id_tarifa === 0) {
            return (string) _("no sé cuál he de borrar");
        }

        $oTipoTarifa = $this->tipoTarifaRepository->findById($id_tarifa);
        if ($oTipoTarifa === null) {
            return (string) _("no se encuentra la tarifa");
        }

        if ($this->tipoTarifaRepository->Eliminar($oTipoTarifa) === false) {
            return (string) _("hay un error, no se ha borrado");
        }

        return '';
    }
}

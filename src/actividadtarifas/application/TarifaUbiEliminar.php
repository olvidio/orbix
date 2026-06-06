<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use function src\shared\domain\helpers\input_int;

/**
 * Mutacion: elimina una `TarifaUbi`.
 */
final class TarifaUbiEliminar
{
    public function __construct(
        private TarifaUbiRepositoryInterface $tarifaUbiRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_item = input_int($input, 'id_item');
        if ($id_item === 0) {
            return (string) _("no sé cuál he de borrar");
        }

        $oTarifaUbi = $this->tarifaUbiRepository->findById($id_item);
        if ($oTarifaUbi === null) {
            return (string) _("no se encuentra la tarifa");
        }

        if ($this->tarifaUbiRepository->Eliminar($oTarifaUbi) === false) {
            return (string) _("hay un error, no se ha borrado")
                . "\n" . $this->tarifaUbiRepository->getErrorTxt();
        }

        return '';
    }
}

<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Mutacion: actualiza en lote las cantidades (`cantidad`) de varias
 * `TarifaUbi` desde el estudio economico de casa.
 */
final class TarifaUbiUpdateInc
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
        $inc_cantidad = $input['inc_cantidad'] ?? null;
        if (!is_array($inc_cantidad) || $inc_cantidad === []) {
            return '';
        }

        $errores = [];

        foreach ($inc_cantidad as $key => $cantidad) {
            $tarifa = strtok((string) $key, '#');
            $id_item = (int) strtok('#');
            $cantidadStr = is_scalar($cantidad) ? (string) $cantidad : '0';
            $cantidadNum = (int) round((float) $cantidadStr);
            unset($tarifa);

            if ($id_item === 0 && $cantidadNum === 0) {
                continue;
            }
            if ($id_item === 0) {
                continue;
            }

            $oTarifaUbi = $this->tarifaUbiRepository->findById($id_item);
            if ($oTarifaUbi === null) {
                continue;
            }
            $oTarifaUbi->setCantidad($cantidadNum);

            if ($this->tarifaUbiRepository->Guardar($oTarifaUbi) === false) {
                $errores[] = (string) _("hay un error, no se ha guardado")
                    . "\n" . $this->tarifaUbiRepository->getErrorTxt();
            }
        }

        return implode("\n", $errores);
    }
}

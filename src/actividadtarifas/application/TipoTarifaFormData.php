<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Data builder para el formulario modificar/nuevo de `TipoTarifa`.
 */
final class TipoTarifaFormData
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   id_tarifa: string,
     *   es_nuevo: bool,
     *   letra: string,
     *   modo: int,
     *   observ: string,
     *   opciones_modo: array<int,string>
     * }
     */
    public function execute(array $input): array
    {
        $id_tarifa = FuncTablasSupport::inputString($input, 'id_tarifa');
        $es_nuevo = $id_tarifa === '' || $id_tarifa === 'nuevo';

        $letra = '';
        $modo = 0;
        $observ = '';

        if (!$es_nuevo) {
            $oTipoTarifa = $this->tipoTarifaRepository->findById((int) $id_tarifa);
            if ($oTipoTarifa !== null) {
                $letra = (string) $oTipoTarifa->getLetra();
                $modo = (int) $oTipoTarifa->getModo();
                $observ = (string) $oTipoTarifa->getObserv();
            }
        }

        return [
            'id_tarifa' => $es_nuevo ? 'nuevo' : $id_tarifa,
            'es_nuevo' => $es_nuevo,
            'letra' => $letra,
            'modo' => $modo,
            'observ' => $observ,
            'opciones_modo' => TarifaModoId::getArrayModo(),
        ];
    }
}

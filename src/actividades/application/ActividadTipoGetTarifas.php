<?php

namespace src\actividades\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Desplegable de tarifas de la sección sv/sf, sin opción seleccionada.
 * Al cambiar sv/sf el formulario debe quedar en blanco hasta concretar el tipo.
 */
class ActividadTipoGetTarifas
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool}
     */
    public function execute(array $input = []): array
    {
        $sfsv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');
        $sfsvInt = is_numeric($sfsv) ? (int) $sfsv : 0;

        return [
            'id' => 'id_tarifa',
            'opciones' => OpcionesDesplegable::enOrden($this->tipoTarifaRepository->getArrayTipoTarifas($sfsvInt)),
            'selected' => '',
            'blanco' => true,
        ];
    }
}

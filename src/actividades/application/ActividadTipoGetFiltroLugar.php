<?php

namespace src\actividades\application;

use src\shared\domain\helpers\OpcionesDesplegable;
use src\ubis\application\services\DelegacionDropdown;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Devuelve el payload (id, opciones, blanco, action) del desplegable de
 * filtros de lugar (delegaciones + regiones). El frontend construye el
 * `<select>`.
 */
class ActividadTipoGetFiltroLugar
{
    public function __construct(
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, blanco: bool, action: string}
     */
    public function execute(array $input = []): array
    {
        $sfsv = FuncTablasSupport::inputString($input, 'entrada');
        $sfsvInt = is_numeric($sfsv) ? (int) $sfsv : 0;

        return [
            'id' => 'filtro_lugar',
            'opciones' => OpcionesDesplegable::enOrden($this->delegacionDropdown->dlURegionesFiltro($sfsvInt)),
            'blanco' => true,
            'action' => 'fnjs_lugar()',
        ];
    }
}

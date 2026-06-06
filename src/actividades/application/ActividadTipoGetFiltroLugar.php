<?php

namespace src\actividades\application;

use src\ubis\application\services\DelegacionDropdown;

use function src\shared\domain\helpers\input_string;

/**
 * Devuelve el payload (id, opciones, blanco, action) del desplegable de
 * filtros de lugar (delegaciones + regiones). El frontend construye el
 * `<select>`.
 */
class ActividadTipoGetFiltroLugar
{
    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: array<int|string,string>, blanco: bool, action: string}
     */
    public function execute(array $input = []): array
    {
        $sfsv = input_string($input, 'entrada');
        $sfsvInt = is_numeric($sfsv) ? (int) $sfsv : 0;

        return [
            'id' => 'filtro_lugar',
            'opciones' => DelegacionDropdown::dlURegionesFiltro($sfsvInt),
            'blanco' => true,
            'action' => 'fnjs_lugar()',
        ];
    }
}

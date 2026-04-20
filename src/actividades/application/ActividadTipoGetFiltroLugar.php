<?php

namespace src\actividades\application;

use src\ubis\application\services\DelegacionDropdown;

/**
 * Devuelve el payload (id, opciones, blanco, action) del desplegable de
 * filtros de lugar (delegaciones + regiones). El frontend construye el
 * `<select>`.
 */
class ActividadTipoGetFiltroLugar
{
    /**
     * @param array $input
     * @return array{id: string, opciones: array<int|string,string>, blanco: bool, action: string}
     */
    public function execute(array $input = []): array
    {
        $sfsv = (string)($input['entrada'] ?? '');

        return [
            'id' => 'filtro_lugar',
            'opciones' => DelegacionDropdown::dlURegionesFiltro($sfsv),
            'blanco' => true,
            'action' => 'fnjs_lugar()',
        ];
    }
}

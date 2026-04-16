<?php

namespace src\ubis\application;

use src\ubis\application\services\DelegacionDropdown;

/**
 * Opciones del formulario delegaciones (traslado de ubis).
 *
 * @return array{opciones_dl_destino: array<string, string>}
 */
final class DelegacionQueData
{
    public static function execute(): array
    {
        return [
            'opciones_dl_destino' => DelegacionDropdown::listaRegDele(false),
        ];
    }
}

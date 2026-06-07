<?php

namespace src\ubis\application;

use src\ubis\application\services\DelegacionDropdown;

/**
 * Opciones del formulario delegaciones (traslado de ubis).
 */
final class DelegacionQueData
{
    public function __construct(
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * @return array{opciones_dl_destino: array<string, string>}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'opciones_dl_destino' => $this->delegacionDropdown->listaRegDele(false),
        ];
    }
}

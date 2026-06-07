<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Devuelve solo el valor por defecto del parámetro `contribucion_reserva`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ContribucionReservaDefaultData
{
    public function __construct(
        private readonly ContribucionReserva $contribucionReserva,
    ) {
    }

    /**
     * @return array{default: string}
     */
    public function execute(): array
    {
        
        return [
            'default' => (string)$this->contribucionReserva->getDefault(),
        ];
    }
}

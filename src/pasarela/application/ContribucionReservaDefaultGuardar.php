<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Actualiza el valor por defecto del parámetro `contribucion_reserva`.
 */
final class ContribucionReservaDefaultGuardar
{
    public function __construct(
        private readonly ContribucionReserva $contribucionReserva,
    ) {
    }

    public function execute(string $default): string
    {
        if ($default === '') {
            return _('Falta valor por defecto');
        }
        if (!is_numeric($default) || (int)$default < 0 || (int)$default > 100) {
            return _('Debe ser un numero entero del 1 al 100');
        }
        
        $this->contribucionReserva->setDefault((int)$default);
        return '';
    }
}

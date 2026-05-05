<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Devuelve solo el valor por defecto del parámetro `contribucion_reserva`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ContribucionReservaDefaultData
{
    public static function execute(): array
    {
        $oContribucionReserva = new ContribucionReserva();
        return [
            'default' => (string)$oContribucionReserva->getDefault(),
        ];
    }
}

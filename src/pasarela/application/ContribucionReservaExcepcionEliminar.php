<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Elimina una excepción del parámetro `contribucion_reserva` para un
 * `id_tipo_activ` concreto.
 */
final class ContribucionReservaExcepcionEliminar
{
    public function __construct(
        private readonly ContribucionReserva $contribucionReserva,
    ) {
    }

    public function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        
        $this->contribucionReserva->delContribucionReserva($id_tipo_activ);
        return '';
    }
}

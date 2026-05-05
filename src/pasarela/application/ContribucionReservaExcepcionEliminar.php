<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Elimina una excepción del parámetro `contribucion_reserva` para un
 * `id_tipo_activ` concreto.
 */
final class ContribucionReservaExcepcionEliminar
{
    public static function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        $oContribucionReserva = new ContribucionReserva();
        $oContribucionReserva->delContribucionReserva($id_tipo_activ);
        return '';
    }
}

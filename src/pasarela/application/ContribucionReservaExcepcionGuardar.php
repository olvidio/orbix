<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionReserva;

/**
 * Inserta o actualiza una excepción del parámetro `contribucion_reserva`
 * para un `id_tipo_activ` concreto.
 */
final class ContribucionReservaExcepcionGuardar
{
    public function __construct(
        private readonly ContribucionReserva $contribucionReserva,
    ) {
    }

    public function execute(string $id_tipo_activ, string $valor): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        if ($valor === '') {
            return _('Falta valor de contribución');
        }
        if (!is_numeric($valor) || (int)$valor < 0 || (int)$valor > 100) {
            return _('Debe ser un numero entero del 1 al 100');
        }
        
        $this->contribucionReserva->addContribucionReserva($id_tipo_activ, (int)$valor);
        return '';
    }
}

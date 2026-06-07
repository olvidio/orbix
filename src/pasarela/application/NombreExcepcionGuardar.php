<?php

namespace src\pasarela\application;

use src\pasarela\domain\Nombre;

/**
 * Inserta o actualiza una excepción del parámetro `nombre` para un
 * `id_tipo_activ` concreto.
 */
final class NombreExcepcionGuardar
{
    public function __construct(
        private readonly Nombre $nombre,
    ) {
    }

    public function execute(string $id_tipo_activ, string $valor): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        if ($valor === '') {
            return _('Falta nombre');
        }
        
        $this->nombre->addNombre($id_tipo_activ, $valor);
        return '';
    }
}

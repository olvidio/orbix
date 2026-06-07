<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Elimina una excepción del parámetro `fecha_activacion` para un `id_tipo_activ` concreto.
 */
final class ActivacionExcepcionEliminar
{
    public function __construct(
        private readonly Activacion $activacion,
    ) {
    }

    public function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        
        $this->activacion->delActivacion($id_tipo_activ);
        return '';
    }
}

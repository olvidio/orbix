<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Actualiza el valor por defecto del parámetro `fecha_activacion`.
 */
final class ActivacionDefaultGuardar
{
    public function __construct(
        private readonly Activacion $activacion,
    ) {
    }

    public function execute(string $default): string
    {
        if ($default === '') {
            return _('Falta valor por defecto');
        }
        
        $this->activacion->setDefault($default);
        return '';
    }
}

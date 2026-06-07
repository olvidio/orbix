<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Devuelve solo el valor por defecto del parámetro `fecha_activacion`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ActivacionDefaultData
{
    public function __construct(
        private readonly Activacion $activacion,
    ) {
    }

    /**
     * @return array{default: string}
     */
    public function execute(): array
    {
        
        return [
            'default' => (string)$this->activacion->getDefault(),
        ];
    }
}

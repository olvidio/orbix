<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Devuelve solo el valor por defecto del parámetro `fecha_activacion`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ActivacionDefaultData
{
    public static function execute(): array
    {
        $oActivacion = new Activacion();
        return [
            'default' => (string)$oActivacion->getDefault(),
        ];
    }
}

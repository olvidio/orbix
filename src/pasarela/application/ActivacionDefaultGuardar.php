<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Actualiza el valor por defecto del parámetro `fecha_activacion`.
 */
final class ActivacionDefaultGuardar
{
    public static function execute(string $default): string
    {
        if ($default === '') {
            return _('Falta valor por defecto');
        }
        $oActivacion = new Activacion();
        $oActivacion->setDefault($default);
        return '';
    }
}

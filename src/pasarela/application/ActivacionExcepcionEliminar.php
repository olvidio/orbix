<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Elimina una excepción del parámetro `fecha_activacion` para un `id_tipo_activ` concreto.
 */
final class ActivacionExcepcionEliminar
{
    public static function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        $oActivacion = new Activacion();
        $oActivacion->delActivacion($id_tipo_activ);
        return '';
    }
}

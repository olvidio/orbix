<?php

namespace src\pasarela\application;

use src\pasarela\domain\Activacion;

/**
 * Inserta o actualiza una excepción del parámetro `fecha_activacion` para un
 * `id_tipo_activ` concreto.
 */
final class ActivacionExcepcionGuardar
{
    public static function execute(string $id_tipo_activ, string $valor): string
    {
        $error_txt = '';
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        if ($valor === '') {
            return _('Falta valor de activación');
        }
        $oActivacion = new Activacion();
        $oActivacion->addActivacion($id_tipo_activ, $valor);
        return $error_txt;
    }
}

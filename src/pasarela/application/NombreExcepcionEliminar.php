<?php

namespace src\pasarela\application;

use src\pasarela\domain\Nombre;

/**
 * Elimina una excepción del parámetro `nombre` para un `id_tipo_activ` concreto.
 */
final class NombreExcepcionEliminar
{
    public static function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        $oNombre = new Nombre();
        $oNombre->delNombre($id_tipo_activ);
        return '';
    }
}

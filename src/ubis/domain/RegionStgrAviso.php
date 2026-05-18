<?php

namespace src\ubis\domain;

/**
 * Mensajes de configuración cuando una dl no tiene región del stgr asignada.
 */
final class RegionStgrAviso
{
    public static function esDlSinRegion(\Throwable $e): bool
    {
        if (!$e instanceof \RuntimeException) {
            return false;
        }
        $msg = $e->getMessage();

        return str_contains($msg, _('falta indicar a que región del stgr pertenece la dl:'))
            || str_contains($msg, 'región del stgr pertenece la dl');
    }

    public static function append(string $aviso, string $mensaje): string
    {
        $mensaje = trim($mensaje);
        if ($mensaje === '') {
            return $aviso;
        }
        if ($aviso !== '' && str_contains($aviso, $mensaje)) {
            return $aviso;
        }

        return $aviso === '' ? $mensaje : $aviso . '<br>' . $mensaje;
    }
}

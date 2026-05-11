<?php

namespace src\procesos\domain;

use src\permisos\domain\PermisosActividades;

/**
 * Bits "afecta a" de permisos de actividad ({@see PermAfectados}).
 */
final class PermAfectadosBits
{
    /**
     * @return array<string, int>
     */
    public static function map(): array
    {
        return PermisosActividades::AFECTA;
    }

    /**
     * Equivalente a {@see XPermisos::lista_tiene_txt()} con el mapa de afectados.
     */
    public static function listaTieneTxt(int $bin): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach (self::map() as $nom => $num) {
            if ($bin & $num) {
                $i++;
                if ($i > 1) {
                    $txt .= ', ';
                }
                $txt .= $nom;
            }
        }

        return $txt;
    }
}

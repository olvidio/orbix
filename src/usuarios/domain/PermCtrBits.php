<?php

namespace src\usuarios\domain;

/**
 * Permisos CTR (mismo mapa que {@see PermCtr}).
 */
final class PermCtrBits
{
    /**
     * @return array<string, int>
     */
    public static function map(): array
    {
        return [
            'nada' => 0,
            'ver' => 1,
            'cl' => 3,
            'sacd' => 7,
            'd' => 15,
        ];
    }
}

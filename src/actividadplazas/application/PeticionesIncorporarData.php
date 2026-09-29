<?php

namespace src\actividadplazas\application;

use src\shared\security\HashB;

/**
 * Lectura mínima de la pantalla `incorporar_peticion`: emite la cápsula
 * acción-only para `/src/actividadplazas/peticiones_incorporar`.
 */
final class PeticionesIncorporarData
{
    /**
     * @return array{ctx_incorporar: string}
     */
    public function execute(): array
    {
        return [
            'ctx_incorporar' => HashB::sign('peticiones_incorporar'),
        ];
    }
}

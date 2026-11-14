<?php

namespace src\encargossacd\application;

use src\shared\security\HashB;

/**
 * Lectura mínima: cápsula para «Eliminar encargos de centros inexistentes»
 * ({@see EncargoComprobacionesCtr}). La firma la pantalla de listas de encargos; sin ella
 * `/src/encargossacd/comprobaciones_ctr` no borra nada.
 */
final class EncargoComprobacionesCtrData
{
    public const ACCION = 'encargos_comprobaciones_ctr';

    /**
     * @return array{ctx_comprobar: string}
     */
    public function execute(): array
    {
        return [
            'ctx_comprobar' => HashB::sign(self::ACCION),
        ];
    }
}

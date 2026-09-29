<?php

namespace src\encargossacd\application;

use src\shared\security\HashB;

/**
 * Lectura mínima: cápsula acción-only para crear la tabla de propuestas.
 */
final class PropuestasCrearTablaData
{
    /**
     * @return array{ctx_crear_tabla: string}
     */
    public function execute(): array
    {
        return [
            'ctx_crear_tabla' => HashB::sign('propuestas_ajax_crear_tabla'),
        ];
    }
}

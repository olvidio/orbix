<?php

namespace src\encargossacd\application;

use src\shared\security\HashB;

/**
 * Lectura mínima: cápsula acción-only para aprobar propuestas.
 */
final class PropuestasAprobarData
{
    /**
     * @return array{ctx_aprobar: string}
     */
    public function execute(): array
    {
        return [
            'ctx_aprobar' => HashB::sign('propuestas_aprobar'),
        ];
    }
}

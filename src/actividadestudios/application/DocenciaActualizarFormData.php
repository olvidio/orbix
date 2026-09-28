<?php

declare(strict_types=1);

namespace src\actividadestudios\application;

use src\shared\security\HashB;

/**
 * Lectura del formulario «actualizar docencia»: emite la cápsula HashB
 * de la mutación masiva (sin identidad de registro).
 */
final class DocenciaActualizarFormData
{
    /**
     * @return array{ctx_actualizar: string}
     */
    public function execute(): array
    {
        return [
            'ctx_actualizar' => HashB::sign('docencia_actualizar'),
        ];
    }
}

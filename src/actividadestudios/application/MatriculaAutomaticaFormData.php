<?php

declare(strict_types=1);

namespace src\actividadestudios\application;

use src\shared\security\HashB;

/**
 * Lectura previa a matricular automáticamente desde el menú (todas las
 * personas activas). Cápsula de acción, sin identidad de fila.
 */
final class MatriculaAutomaticaFormData
{
    /**
     * @return array{ctx_auto: string}
     */
    public function execute(): array
    {
        return [
            'ctx_auto' => HashB::sign('matricula_automatica'),
        ];
    }
}

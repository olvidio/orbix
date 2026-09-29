<?php

namespace src\actividades\application;

use src\shared\security\HashB;

/**
 * Emite la cápsula acción-only de `actividad_nuevo_curso_ejecutar`.
 * Los años origen/destino siguen siendo campos de negocio del formulario.
 */
final class ActividadNuevoCursoFormData
{
    /**
     * @return array{ctx_ejecutar: string}
     */
    public function execute(): array
    {
        return [
            'ctx_ejecutar' => HashB::sign('actividad_nuevo_curso_ejecutar'),
        ];
    }
}

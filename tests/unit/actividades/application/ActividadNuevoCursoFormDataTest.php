<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadNuevoCursoFormData;
use src\shared\security\HashB;

final class ActividadNuevoCursoFormDataTest extends TestCase
{
    public function test_emite_ctx_accion_only(): void
    {
        $out = (new ActividadNuevoCursoFormData())->execute();
        $this->assertSame([], HashB::open($out['ctx_ejecutar'], 'actividad_nuevo_curso_ejecutar'));
    }
}

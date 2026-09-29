<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\DocenciaActualizarFormData;
use src\actividadestudios\application\MatriculaAutomaticaFormData;
use src\shared\security\HashB;

final class DocenciaYMatriculaAutomaticaFormDataTest extends TestCase
{
    public function test_docencia_emite_capsula_de_accion(): void
    {
        $out = (new DocenciaActualizarFormData())->execute();
        $this->assertSame([], HashB::open($out['ctx_actualizar'], 'docencia_actualizar'));
    }

    public function test_matricula_automatica_menu_emite_capsula_de_accion(): void
    {
        $out = (new MatriculaAutomaticaFormData())->execute();
        $this->assertSame([], HashB::open($out['ctx_auto'], 'matricula_automatica'));
    }
}

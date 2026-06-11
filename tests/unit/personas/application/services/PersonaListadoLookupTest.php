<?php

namespace Tests\unit\personas\application\services;

use PHPUnit\Framework\TestCase;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\services\PersonaListadoLookup;
use src\personas\domain\entity\PersonaDl;

final class PersonaListadoLookupTest extends TestCase
{
    public function test_un_solo_error_por_alumno_si_no_se_encuentra(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())->method('findPersonaParaListado')->willReturn(null);

        $lookup = new PersonaListadoLookup($finder);
        $msgErr = '';
        $problemas = [];

        $this->assertNull($lookup->resolver(100, $msgErr, $problemas));
        $this->assertNull($lookup->resolver(100, $msgErr, $problemas));

        $this->assertSame(1, substr_count($msgErr, 'id_nom: 100'));
    }

    public function test_solo_un_error_por_alumno_entre_persona_y_actividad(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn(null);

        $lookup = new PersonaListadoLookup($finder);
        $msgErr = '';
        $problemas = [];

        $lookup->resolver(100, $msgErr, $problemas);
        $lookup->reportarErrorAlumno(
            100,
            $msgErr,
            PersonaListadoLookup::mensajeActividadNoEncontrada(200, 100),
        );

        $this->assertSame(1, substr_count($msgErr, '100'));
    }

    public function test_devuelve_persona_sin_error(): void
    {
        $oPersona = $this->createMock(PersonaDl::class);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn($oPersona);

        $lookup = new PersonaListadoLookup($finder);
        $msgErr = '';
        $problemas = [];

        $this->assertSame($oPersona, $lookup->resolver(50, $msgErr, $problemas));
        $this->assertSame('', $msgErr);
    }
}

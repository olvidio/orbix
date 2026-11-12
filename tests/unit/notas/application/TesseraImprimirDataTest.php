<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\application\TesseraImprimirData;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;

/**
 * `TesseraImprimirData`: datos para imprimir la tessera (nombre, plan y filas), con los mismos avisos que
 * `TesseraVerData`.
 */
final class TesseraImprimirDataTest extends TestCase
{
    private function useCase(PersonaFinderService $finder): TesseraImprimirData
    {
        $notas = $this->createStub(PersonaNotaRepositoryInterface::class);
        $notas->method('matching')->willReturn([]);
        $fil1 = new Asignatura();
        $fil1->setId_asignatura(1101);
        $fil1->setIdNivelVo(new NivelId(1101));
        $fil1->setNombreAsignaturaVo(new AsignaturaName('Philosophia I'));
        $asignaturas = $this->createStub(AsignaturaRepositoryInterface::class);
        $asignaturas->method('matching')->willReturn([$fil1]);

        return new TesseraImprimirData(
            new Tesera(new ExpedienteNotasPersona($notas), $asignaturas, new PlanEstudiosDePersona($notas)),
            $finder,
        );
    }

    /**
     * Regresión: «ver tessera» ya aceptaba personas no activas (tessera histórica), pero «imprimir» buscaba solo
     * situación 'A' y respondía «No encuentro persona».
     */
    public function test_imprime_la_tessera_de_una_persona_no_activa(): void
    {
        $persona = $this->createStub(PersonaDl::class);
        $persona->method('getId_schema')->willReturn(1001);
        $persona->method('getNombreApellidos')->willReturn('Juan Pérez');
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())->method('findPersonaEnGlobalIncluyendoNoActivos')->with(10011)->willReturn($persona);
        $finder->expects($this->never())->method('findPersonaEnGlobalODePaso');

        $r = $this->useCase($finder)->execute(10011);

        $this->assertSame('Juan Pérez', $r['nom']);
        $this->assertSame(Tesera::PLAN_NUEVO, $r['plan']);
        $this->assertCount(1, $r['filas']);
        $this->assertSame('Philosophia I', $r['filas'][0]['nombre']);
        $this->assertTrue($r['filas'][0]['pendiente']);
    }

    public function test_persona_no_encontrada(): void
    {
        $finder = $this->createStub(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalIncluyendoNoActivos')->willReturn(null);

        $r = $this->useCase($finder)->execute(10011);

        $this->assertSame(['aviso'], array_keys($r));
        $this->assertStringContainsString('10011', $r['aviso']);
    }

    public function test_persona_de_una_dl_sin_esquema_da_aviso(): void
    {
        $persona = $this->createStub(PersonaDl::class);
        $persona->method('getId_schema')->willReturn(0);
        $persona->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $persona->method('getDl')->willReturn('dlz');
        $finder = $this->createStub(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalIncluyendoNoActivos')->willReturn($persona);

        $r = $this->useCase($finder)->execute(10011);

        $this->assertSame(['aviso'], array_keys($r));
        $this->assertStringContainsString('Pérez, Juan', $r['aviso']);
    }

    public function test_persona_de_paso_sin_esquema_si_se_imprime(): void
    {
        $persona = $this->createStub(PersonaEx::class);
        $persona->method('getId_schema')->willReturn(0);
        $persona->method('getNombreApellidos')->willReturn('Juan de Paso');
        $finder = $this->createStub(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalIncluyendoNoActivos')->willReturn($persona);

        $r = $this->useCase($finder)->execute(-1001123);

        $this->assertSame('Juan de Paso', $r['nom']);
    }
}

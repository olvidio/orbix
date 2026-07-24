<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\CambioAvisoTxtBuilder;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

final class CambioAvisoTxtBuilderTest extends TestCase
{
    private function createBuilder(
        ActividadAll $actividad,
        PersonaFinderService $finder,
    ): CambioAvisoTxtBuilder {
        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->willReturn($actividad);

        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->method('findById')->willReturn(null);

        return new CambioAvisoTxtBuilder(
            new ActividadParaAvisoLookup($allRepository, $exRepository),
            $this->createMock(CambioRepositoryInterface::class),
            $finder,
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(RepeticionRepositoryInterface::class),
            $this->createMock(ActividadFaseRepositoryInterface::class),
        );
    }

    public function test_delete_asistente_muestra_nombre_no_id_nom(): void
    {
        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getPrefApellidosNombre')->willReturn('García López, Juan');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobal')
            ->with(10012845)
            ->willReturn($persona);

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv agd Castelldaura');

        $builder = $this->createBuilder($actividad, $finder);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_DELETE);
        $cambio->setObjeto('Asistente');
        $cambio->setId_activ(10);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old('10012845');
        $cambio->setValor_new(null);

        $txt = $builder->build($cambio);

        $this->assertIsString($txt);
        $this->assertStringContainsString('García López, Juan', $txt);
        $this->assertStringNotContainsString('10012845', $txt);
        $this->assertStringContainsString('cv agd Castelldaura', $txt);
    }

    public function test_insert_asistente_muestra_nombre_no_id_nom(): void
    {
        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getPrefApellidosNombre')->willReturn('Pérez, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobal')
            ->with(55)
            ->willReturn($persona);

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv n prueba');

        $builder = $this->createBuilder($actividad, $finder);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_INSERT);
        $cambio->setObjeto('AsistenteDl');
        $cambio->setId_activ(3);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old(null);
        $cambio->setValor_new('55');

        $txt = $builder->build($cambio);

        $this->assertIsString($txt);
        $this->assertStringContainsString('Pérez, Ana', $txt);
        $this->assertStringNotContainsString('"55"', $txt);
    }
}

<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use src\actividades\domain\value_objects\StatusId;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\AvisosGenerarTabla;
use src\cambios\application\legacy\Avisos;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

final class AvisosGenerarTablaSinProcesosTest extends TestCase
{
    private function createUseCase(ActividadAllRepositoryInterface $allRepository): AvisosGenerarTabla
    {
        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->method('findById')->willReturn(null);

        return new AvisosGenerarTabla(
            $this->createMock(Avisos::class),
            $this->createMock(CambioRepositoryInterface::class),
            new ActividadParaAvisoLookup($allRepository, $exRepository),
            $this->createMock(ImportadaRepositoryInterface::class),
            $this->createMock(TipoDeActividadRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(TareaProcesoRepositoryInterface::class),
            $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class),
            $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class),
        );
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: bool, 3: bool, 4: int}>
     */
    public static function evaluarFaseCorrectaProvider(): array
    {
        return [
            'aviso_on coincide' => [StatusId::PROYECTO, StatusId::PROYECTO, true, false, 1],
            'aviso_on no coincide' => [StatusId::ACTUAL, StatusId::PROYECTO, true, false, 0],
            'aviso_off actividad fuera del estado' => [StatusId::ACTUAL, StatusId::PROYECTO, false, true, 1],
            'aviso_off actividad en el estado' => [StatusId::PROYECTO, StatusId::PROYECTO, false, true, 0],
            'sin flags' => [StatusId::PROYECTO, StatusId::PROYECTO, false, false, 0],
        ];
    }

    /**
     * @dataProvider evaluarFaseCorrectaProvider
     */
    public function test_evaluar_fase_correcta_sin_procesos(
        int $statusActual,
        int $idFaseRef,
        bool $avisoOn,
        bool $avisoOff,
        int $esperado,
    ): void {
        $method = new ReflectionMethod(AvisosGenerarTabla::class, 'evaluarFaseCorrectaSinProcesos');
        $method->setAccessible(true);

        $resultado = $method->invoke(null, $statusActual, $idFaseRef, $avisoOn, $avisoOff);

        $this->assertSame($esperado, $resultado);
    }

    public function test_status_actividad_para_matching_usa_estado_actual(): void
    {
        $actividad = $this->createMock(\src\actividades\domain\entity\ActividadAll::class);
        $actividad->method('getStatus')->willReturn(StatusId::ACTUAL);

        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->with(42)->willReturn($actividad);

        $useCase = $this->createUseCase($allRepository);
        $method = new ReflectionMethod(AvisosGenerarTabla::class, 'statusActividadParaMatching');
        $method->setAccessible(true);

        $resultado = $method->invoke($useCase, 42, StatusId::PROYECTO);

        $this->assertSame(StatusId::ACTUAL, $resultado);
    }

    public function test_status_actividad_para_matching_fallback_id_status_cmb(): void
    {
        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->willReturn(null);

        $useCase = $this->createUseCase($allRepository);
        $method = new ReflectionMethod(AvisosGenerarTabla::class, 'statusActividadParaMatching');
        $method->setAccessible(true);

        $resultado = $method->invoke($useCase, 0, StatusId::TERMINADA);

        $this->assertSame(StatusId::TERMINADA, $resultado);
    }

    public function test_status_actividad_para_matching_sin_datos_devuelve_null(): void
    {
        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);

        $useCase = $this->createUseCase($allRepository);
        $method = new ReflectionMethod(AvisosGenerarTabla::class, 'statusActividadParaMatching');
        $method->setAccessible(true);

        $resultado = $method->invoke($useCase, 0, 0);

        $this->assertNull($resultado);
    }

    public function test_status_de_fase_referencia_traduce_fase_a_status(): void
    {
        $tipoRepo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipoActividad = $this->createMock(\src\actividades\domain\entity\TipoDeActividad::class);
        $tipoActividad->method('getId_tipo_proceso')->willReturn(99);
        $tipoRepo->method('getTiposDeActividades')->willReturn([$tipoActividad]);

        $tareaRepo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $tarea = $this->createMock(\src\procesos\domain\entity\TareaProceso::class);
        $tarea->method('getStatus')->willReturn(StatusId::ACTUAL);
        $tareaRepo->method('getTareasProceso')->willReturn([$tarea]);

        $useCase = $this->createUseCase($this->createMock(ActividadAllRepositoryInterface::class));
        $method = new ReflectionMethod(AvisosGenerarTabla::class, 'statusDeFaseReferencia');
        $method->setAccessible(true);

        $resultado = $method->invoke(
            $useCase,
            501,
            271000,
            $tipoRepo,
            $tareaRepo,
        );

        $this->assertSame(StatusId::ACTUAL, $resultado);
    }
}

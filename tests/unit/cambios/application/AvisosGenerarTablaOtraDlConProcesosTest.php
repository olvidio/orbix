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

/**
 * Regresión: avisos cross-dl con módulo procesos instalado.
 *
 * {@see AvisosGenerarTablaIntegracionTest} cubría cambios de otra dl solo con
 * prepararEntornoCambiosSinProcesos() e id_schema=3000 (INSERT en public.av_cambios).
 * En producción el fallo aparecía con procesos ON, id_schema del origen (p. ej. 3005)
 * y json_fases con ids de fase que no coinciden con id_fase_ref de la preferencia.
 */
final class AvisosGenerarTablaOtraDlConProcesosTest extends TestCase
{
    public function test_regresion_in_array_sobre_json_fases_no_apuntaria(): void
    {
        $idFaseRefPref = 501;
        $jsonFasesCambio = [601, 602];

        $this->assertNotContains(
            $idFaseRefPref,
            $jsonFasesCambio,
            'Escenario dlp→dlb: la fase de la pref no está en json_fases del cambio',
        );

        $faseCorrectaLogicaAntigua = in_array($idFaseRefPref, $jsonFasesCambio, true) ? 1 : 0;
        $this->assertSame(0, $faseCorrectaLogicaAntigua);
    }

    public function test_otra_dl_con_procesos_evalua_por_status_traduciendo_fase_ref(): void
    {
        $idFaseRefPref = 501;
        $jsonFasesCambio = [601, 602];
        $this->assertSame(0, in_array($idFaseRefPref, $jsonFasesCambio, true) ? 1 : 0);

        $tipoRepo = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipoActividad = $this->createMock(\src\actividades\domain\entity\TipoDeActividad::class);
        $tipoActividad->method('getId_tipo_proceso')->willReturn(99);
        $tipoRepo->method('getTiposDeActividades')->willReturn([$tipoActividad]);

        $tareaRepo = $this->createMock(TareaProcesoRepositoryInterface::class);
        $tarea = $this->createMock(\src\procesos\domain\entity\TareaProceso::class);
        $tarea->method('getStatus')->willReturn(StatusId::ACTUAL);
        $tareaRepo->method('getTareasProceso')->willReturn([$tarea]);

        $useCase = $this->createUseCase();
        $statusDeFase = new ReflectionMethod(AvisosGenerarTabla::class, 'statusDeFaseReferencia');
        $statusDeFase->setAccessible(true);
        $evaluar = new ReflectionMethod(AvisosGenerarTabla::class, 'evaluarFaseCorrectaSinProcesos');
        $evaluar->setAccessible(true);

        $statusReferencia = $statusDeFase->invoke(
            $useCase,
            $idFaseRefPref,
            271000,
            $tipoRepo,
            $tareaRepo,
        );
        $faseCorrecta = $evaluar->invoke(
            null,
            StatusId::ACTUAL,
            $statusReferencia,
            true,
            false,
        );

        $this->assertSame(StatusId::ACTUAL, $statusReferencia);
        $this->assertSame(1, $faseCorrecta);
    }

    private function createUseCase(): AvisosGenerarTabla
    {
        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);

        return new AvisosGenerarTabla(
            $this->createMock(Avisos::class),
            $this->createMock(CambioRepositoryInterface::class),
            new ActividadParaAvisoLookup(
                $this->createMock(ActividadAllRepositoryInterface::class),
                $exRepository,
            ),
            $this->createMock(ImportadaRepositoryInterface::class),
            $this->createMock(TipoDeActividadRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(TareaProcesoRepositoryInterface::class),
            $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class),
            $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class),
        );
    }
}

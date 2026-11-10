<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoTipo;
use src\misas\application\CrearNuevoPeriodoData;
use src\misas\application\ImportarPlantillaData;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\domain\value_objects\PlantillaConfig;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use Tests\myTest;

/**
 * Importar una plantilla a otra y crear el plan de un periodo: contrato, borrado y copia de días.
 * Ejecuta las funciones procedurales `misas_importar_plantilla_build` (importar_plantilla_data_build.php)
 * y `misas_crear_nuevo_periodo_build` (crear_nuevo_periodo_data_build.php) a través de sus envoltorios `build()`.
 */
final class ImportarYCrearPeriodoTest extends myTest
{
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoDiaRepositoryInterface&MockObject $encargosDia;
    private EncargoHorarioRepositoryInterface&MockObject $horarios;

    public function setUp(): void
    {
        parent::setUp();
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getId_tipo_enc')->willReturn(8110);
        $this->tipos->method('matching')->willReturn([$t]);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosDia = $this->createMock(EncargoDiaRepositoryInterface::class);
        $this->horarios = $this->createMock(EncargoHorarioRepositoryInterface::class);
    }

    private function encargo(int $id, int $tipo = 8110, string $desc = 'Misa'): Encargo
    {
        $e = $this->createMock(Encargo::class);
        $e->method('getId_enc')->willReturn($id);
        $e->method('getId_tipo_enc')->willReturn($tipo);
        $e->method('getDesc_enc')->willReturn($desc);
        $e->method('getId_ubi')->willReturn(1003);

        return $e;
    }

    private function importar(): ImportarPlantillaData
    {
        return new ImportarPlantillaData($this->tipos, $this->encargosDia, $this->horarios, $this->encargos);
    }

    private function diaOrigen(int $idNom = 10, string $ini = '2001-01-01 10:00', ?string $observ = 'nota'): EncargoDia
    {
        $d = new EncargoDia();
        $d->setUuid_item(\Ramsey\Uuid\Uuid::uuid4()->toString());
        $d->setId_nom($idNom);
        $d->setTstart(new \src\misas\domain\value_objects\EncargoDiaTstart(substr($ini, 0, 10), substr($ini, 11)));
        $d->setTend(new \src\misas\domain\value_objects\EncargoDiaTend(substr($ini, 0, 10), '11:00'));
        if ($observ !== null) {
            $d->setObserv($observ);
        }

        return $d;
    }

    // ---- ImportarPlantillaData ----------------------------------------------------------------------

    public function test_los_accesores_devuelven_los_repositorios_inyectados(): void
    {
        $i = $this->importar();

        $this->assertSame($this->tipos, $i->getEncargoTipoRepository());
        $this->assertSame($this->encargosDia, $i->getEncargoDiaRepository());
        $this->assertSame($this->horarios, $i->getEncargoHorarioRepository());
        $this->assertSame($this->encargos, $i->getEncargoRepository());
    }

    public function test_sin_encargos_en_la_zona_no_se_copia_ni_se_borra_nada(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosDia->expects($this->never())->method('Guardar');
        $this->encargosDia->expects($this->never())->method('Eliminar');

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => 's1']);

        $this->assertSame(['error' => '', 'success' => true], $r);
    }

    public function test_semanal_a_semanal_copia_los_siete_dias_con_sacd_horas_y_observacion(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $this->encargosDia->method('matching')->willReturn([$this->diaOrigen()]);
        $this->encargosDia->expects($this->once())->method('Eliminar');
        $guardados = [];
        $this->encargosDia->expects($this->exactly(7))->method('Guardar')->willReturnCallback(function (EncargoDia $d) use (&$guardados): bool {
            $guardados[] = $d;

            return true;
        });

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => 's1']);

        $this->assertSame(['error' => '', 'success' => true], $r);
        $this->assertCount(7, array_unique(array_map(fn (EncargoDia $d) => $d->getUuid_item(), $guardados)), 'cada copia con su uuid');
        $this->assertSame(['2001-01-01', '2001-01-02', '2001-01-03', '2001-01-04', '2001-01-05', '2001-01-06', '2001-01-07'], array_map(fn (EncargoDia $d) => $d->getTstart()->format('Y-m-d'), $guardados));
        foreach ($guardados as $d) {
            $this->assertSame([10, 20, 'nota', '10:00'], [$d->getId_nom(), $d->getId_enc(), $d->getObserv(), $d->getTstart()->format('H:i')]);
        }
    }

    public function test_semanal_tres_copia_las_tres_semanas_de_cada_dia(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $this->encargosDia->method('matching')->willReturn([$this->diaOrigen()]);
        $this->encargosDia->expects($this->exactly(21))->method('Guardar')->willReturn(true);

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's3', 'tipo_plantilla_destino' => 's3']);

        $this->assertTrue($r['success']);
    }

    /**
     * @dataProvider destinosProvider
     */
    public function test_el_numero_de_dias_copiados_depende_del_destino(string $destino, int $dias): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $this->encargosDia->method('matching')->willReturn([$this->diaOrigen()]);
        $this->encargosDia->expects($this->exactly($dias))->method('Guardar')->willReturn(true);

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => $destino]);

        $this->assertTrue($r['success']);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function destinosProvider(): iterable
    {
        yield 'domingos' => [PlantillaConfig::PLANTILLA_DOMINGOS_UNO, 11];
        yield 'mensual' => [PlantillaConfig::PLANTILLA_MENSUAL_UNO, 35];
    }

    public function test_dos_dias_origen_para_el_mismo_encargo_y_dia_es_un_error(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $this->encargosDia->method('matching')->willReturnOnConsecutiveCalls([], [$this->diaOrigen(), $this->diaOrigen()]);
        $this->encargosDia->expects($this->never())->method('Guardar');

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => 's1']);

        $this->assertFalse($r['success']);
        $this->assertNotSame('', $r['error']);
    }

    public function test_los_errores_de_guardado_se_acumulan_y_marcan_fallo(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $this->encargosDia->method('matching')->willReturn([$this->diaOrigen()]);
        $this->encargosDia->method('Guardar')->willReturn(false);
        $this->encargosDia->method('getErrorTxt')->willReturn('E;');

        $r = $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => 's1']);

        $this->assertSame(['error' => str_repeat('E;', 7), 'success' => false], $r);
    }

    public function test_importar_borra_tambien_los_dias_ya_comunicados_a_diferencia_de_crear_periodo(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $comunicado = $this->diaOrigen();
        $comunicado->setStatus(EncargoDiaStatus::STATUS_COMUNICADO_CTR);
        $this->encargosDia->method('matching')->willReturnOnConsecutiveCalls([$comunicado], ...array_fill(0, 7, []));
        $this->encargosDia->expects($this->once())->method('Eliminar')->with($comunicado);

        $this->importar()->build(['id_zona' => 4, 'tipo_plantilla_origen' => 's1', 'tipo_plantilla_destino' => 's1']);
    }

    // ---- CrearNuevoPeriodoData ----------------------------------------------------------------------

    private function crear(): CrearNuevoPeriodoData
    {
        $iniciales = $this->createMock(InicialesSacdService::class);

        return new CrearNuevoPeriodoData(
            $this->createMock(ZonaSacdRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $iniciales,
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->encargos,
            $this->createMock(EncargoSacdHorarioRepositoryInterface::class),
            $this->tipos,
            $this->encargosDia,
            $this->horarios
        );
    }

    public function test_los_accesores_del_creador_de_periodo_devuelven_lo_inyectado(): void
    {
        $c = $this->crear();

        $this->assertSame($this->encargos, $c->getEncargoRepository());
        $this->assertSame($this->tipos, $c->getEncargoTipoRepository());
        $this->assertSame($this->encargosDia, $c->getEncargoDiaRepository());
        $this->assertSame($this->horarios, $c->getEncargoHorarioRepository());
        $this->assertInstanceOf(ZonaSacdRepositoryInterface::class, $c->getZonaSacdRepository());
        $this->assertInstanceOf(ActividadRepositoryInterface::class, $c->getActividadRepository());
        $this->assertInstanceOf(InicialesSacdService::class, $c->getInicialesSacdService());
        $this->assertInstanceOf(ActividadCargoRepositoryInterface::class, $c->getActividadCargoRepository());
        $this->assertInstanceOf(EncargoSacdHorarioRepositoryInterface::class, $c->getEncargoSacdHorarioRepository());
    }

    public function test_el_periodo_sin_encargos_devuelve_las_columnas_de_cada_dia_y_el_contexto_firmado(): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $r = $this->crear()->build(['id_zona' => 4, 'tipo_plantilla' => 's1', 'periodo' => 'otro', 'empiezamin' => '02/03/2026', 'empiezamax' => '04/03/2026', 'seleccion' => 3]);

        $this->assertSame('', $r['error']);
        $columnas = json_decode($r['columns_cuadricula'], true);
        $this->assertSame('encargo', $columnas[0]['id']);
        $this->assertCount(1 + 2 * 3, $columnas, 'dos columnas por cada día del rango (se incluye el último)');
        $this->assertSame([], $r['data_cuadricula']);
        $this->assertSame(['desc_enc', 4, 's1', 3, 'otro', '02/03/2026', '04/03/2026'], [$r['orden'], $r['id_zona'], $r['tipo_plantilla'], $r['seleccion'], $r['periodo'], $r['empieza_min'], $r['empieza_max']]);
        $this->assertNotSame('', $r['ctx_update']);
        $this->assertSame(EncargoDiaStatus::STATUS_PROPUESTA, $r['status_propuesta']);
        $this->assertNotSame([], $r['estados_opciones']);
    }

    public function test_preparar_el_plan_borra_los_dias_no_comunicados_al_centro_y_conserva_los_comunicados(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargo(20)]);
        $propuesta = $this->diaOrigen();
        $propuesta->setStatus(EncargoDiaStatus::STATUS_PROPUESTA);
        $comunicado = $this->diaOrigen();
        $comunicado->setStatus(EncargoDiaStatus::STATUS_COMUNICADO_CTR);
        $llamadas = 0;
        $this->encargosDia->method('matching')->willReturnCallback(function () use (&$llamadas, $propuesta, $comunicado): array {
            return $llamadas++ === 0 ? [$propuesta, $comunicado] : [$comunicado];
        });
        $eliminados = [];
        $this->encargosDia->method('Eliminar')->willReturnCallback(function (EncargoDia $d) use (&$eliminados): bool {
            $eliminados[] = $d;

            return true;
        });

        $r = $this->crear()->build(['id_zona' => 4, 'tipo_plantilla' => 's1', 'periodo' => 'otro', 'empiezamin' => '02/03/2026', 'empiezamax' => '02/03/2026']);

        $this->assertSame('', $r['error']);
        $this->assertSame([$propuesta], $eliminados);
        $this->assertCount(1, $r['data_cuadricula']);
        $this->assertSame('Misa', $r['data_cuadricula'][0]['encargo']);
    }

    public function test_un_fallo_de_runtime_devuelve_el_mensaje_con_cuadricula_vacia(): void
    {
        $this->encargos->method('matching')->willThrowException(new \RuntimeException('boom'));

        $r = $this->crear()->build(['id_zona' => 4, 'tipo_plantilla' => 's1', 'periodo' => 'otro', 'empiezamin' => '02/03/2026', 'empiezamax' => '03/03/2026']);

        $this->assertSame('boom', $r['error']);
        $this->assertSame('[]', $r['columns_cuadricula']);
        $this->assertSame([], $r['data_cuadricula']);
        $this->assertSame(4, $r['id_zona']);
    }

    /**
     * @dataProvider periodosRelativosProvider
     */
    public function test_los_periodos_relativos_no_necesitan_fechas_del_usuario(string $periodo): void
    {
        $this->encargos->method('matching')->willReturn([]);

        $r = $this->crear()->build(['id_zona' => 4, 'tipo_plantilla' => 's1', 'periodo' => $periodo]);

        $this->assertSame('', $r['error']);
        $this->assertGreaterThanOrEqual(1 + 2 * 7, count(json_decode($r['columns_cuadricula'], true)));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function periodosRelativosProvider(): iterable
    {
        yield 'próxima semana' => ['proxima_semana'];
        yield 'próximo mes' => ['proximo_mes'];
    }
}

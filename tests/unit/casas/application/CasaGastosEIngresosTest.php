<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\casas\application\CasaEcGastosGuardar;
use src\casas\application\CasaIngresoUpdate;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoTipo;
use Tests\myTest;

/** Gastos mensuales de una casa e ingresos de una actividad. */
final class CasaGastosEIngresosTest extends myTest
{
    // ---- CasaEcGastosGuardar ----------------------------------------------------------------------

    public function test_sin_casa_o_anyo_no_se_guarda_nada(): void
    {
        $repo = $this->createMock(UbiGastoRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');
        $uc = new CasaEcGastosGuardar($repo);

        $this->assertFalse($uc->execute(['year' => '2026'])['ok']);
        $this->assertFalse($uc->execute(['id_ubi' => '1003'])['ok']);
        $this->assertFalse($uc->execute(['id_ubi' => 'x', 'year' => '2026'])['ok']);
    }

    public function test_se_graban_tres_apuntes_por_mes_con_fecha_dia_5_y_los_importes_con_coma(): void
    {
        $repo = $this->createMock(UbiGastoRepositoryInterface::class);
        $repo->method('getNewId')->willReturnCallback(static function (): int {
            static $n = 0;

            return ++$n;
        });
        $guardados = [];
        $repo->expects($this->exactly(36))->method('Guardar')->willReturnCallback(function (UbiGasto $g) use (&$guardados): bool {
            $guardados[] = $g;

            return true;
        });

        $r = (new CasaEcGastosGuardar($repo))->execute(['id_ubi' => '1003', 'year' => '2026', 'g1' => '1.234,5', 'ap_sv1' => 100, 'ap_sf1' => '7,25', 'g12' => '9']);

        $this->assertSame(['ok' => true, 'mensaje' => '', 'data' => ''], $r);
        $primero = array_slice($guardados, 0, 3);
        $this->assertSame([UbiGastoTipo::GASTO, UbiGastoTipo::APORTACION_SV, UbiGastoTipo::APORTACION_SF], array_map(fn (UbiGasto $g) => $g->getTipoVo()->value(), $primero));
        $this->assertSame([1.234, 100.0, 7.25], array_map(fn (UbiGasto $g) => $g->getCantidadVo()->value(), $primero), '«1.234,5» (miles con punto) se lee como 1,234: el separador de miles no se contempla');
        $this->assertSame('5/1/2026', $primero[0]->getF_gasto()->getFromLocal());
        $this->assertSame('5/12/2026', $guardados[33]->getF_gasto()->getFromLocal());
        $this->assertSame(9.0, $guardados[33]->getCantidadVo()->value());
        $this->assertSame(1003, $guardados[0]->getId_ubi());
    }

    /**
     * Hallazgo: la documentación dice «borra los existentes y los reinserta», pero no hay ningún borrado:
     * cada guardado añade 36 apuntes nuevos sobre los del año, duplicando los gastos.
     */
    public function test_no_se_borran_los_gastos_anteriores_del_anyo(): void
    {
        $repo = $this->createMock(UbiGastoRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(1);
        $repo->method('Guardar')->willReturn(true);
        $repo->expects($this->never())->method('Eliminar');
        $repo->expects($this->never())->method('matching');

        (new CasaEcGastosGuardar($repo))->execute(['id_ubi' => '1003', 'year' => '2026']);
    }

    public function test_un_fallo_de_guardado_corta_en_el_primer_apunte(): void
    {
        $repo = $this->createMock(UbiGastoRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(1);
        $repo->expects($this->once())->method('Guardar')->willReturn(false);

        $this->assertFalse((new CasaEcGastosGuardar($repo))->execute(['id_ubi' => '1003', 'year' => '2026'])['ok']);
    }

    // ---- CasaIngresoUpdate ----------------------------------------------------------------------

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private IngresoRepositoryInterface&MockObject $ingresos;

    private function ingresoUc(): CasaIngresoUpdate
    {
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->ingresos = $this->createMock(IngresoRepositoryInterface::class);

        return new CasaIngresoUpdate($this->actividades, $this->ingresos);
    }

    public function test_el_ingreso_exige_actividad_y_que_exista(): void
    {
        $uc = $this->ingresoUc();
        $this->actividades->method('findById')->willReturn(null);
        $this->actividades->expects($this->never())->method('Guardar');

        $this->assertFalse($uc->execute([])['ok']);
        $this->assertFalse($uc->execute(['id_activ' => 'x'])['ok']);
        $this->assertFalse($uc->execute(['id_activ' => '3001'])['ok']);
    }

    public function test_tarifa_y_precio_solo_se_cambian_si_se_envian(): void
    {
        $uc = $this->ingresoUc();
        $a = $this->createMock(ActividadAll::class);
        $a->expects($this->once())->method('setTarifa')->with(3);
        $a->expects($this->once())->method('setPrecio')->with(12.5);
        $b = $this->createMock(ActividadAll::class);
        $b->expects($this->never())->method('setTarifa');
        $b->expects($this->never())->method('setPrecio');
        $this->actividades->method('findById')->willReturnOnConsecutiveCalls($a, $b);
        $this->actividades->method('Guardar')->willReturn(true);
        $this->ingresos->method('findById')->willReturn(null);
        $this->ingresos->method('Guardar')->willReturn(true);

        $uc->execute(['id_activ' => '3001', 'id_tarifa' => '3', 'precio' => '12,5']);
        $uc->execute(['id_activ' => '3001', 'precio' => '']);
    }

    public function test_un_ingreso_nuevo_se_crea_con_importe_asistentes_y_observaciones(): void
    {
        $uc = $this->ingresoUc();
        $this->actividades->method('findById')->willReturn($this->createMock(ActividadAll::class));
        $this->actividades->method('Guardar')->willReturn(true);
        $this->ingresos->method('findById')->willReturn(null);
        $guardado = null;
        $this->ingresos->expects($this->once())->method('Guardar')->willReturnCallback(function (Ingreso $i) use (&$guardado): bool {
            $guardado = $i;

            return true;
        });

        $r = $uc->execute(['id_activ' => '3001', 'ingresos' => '300,75', 'num_asistentes' => '20', 'observ' => 'ok']);

        $this->assertSame(['ok' => true, 'mensaje' => '', 'data' => ''], $r);
        $this->assertSame([3001, 300.75, 20, 'ok'], [$guardado->getId_activ(), $guardado->getIngresosVo()->value(), $guardado->getNumAsistentesVo()->value(), $guardado->getObservVo()?->value()]);
    }

    public function test_un_ingreso_existente_se_actualiza_y_no_se_crea_otro(): void
    {
        $uc = $this->ingresoUc();
        $this->actividades->method('findById')->willReturn($this->createMock(ActividadAll::class));
        $this->actividades->method('Guardar')->willReturn(true);
        $existente = new Ingreso();
        $existente->setId_activ(3001);
        $this->ingresos->method('findById')->willReturn($existente);
        $this->ingresos->expects($this->once())->method('Guardar')->with($existente)->willReturn(true);

        $uc->execute(['id_activ' => '3001', 'ingresos' => 5]);

        $this->assertSame(5.0, $existente->getIngresosVo()->value());
    }

    public function test_si_falla_el_guardado_de_la_actividad_no_se_toca_el_ingreso(): void
    {
        $uc = $this->ingresoUc();
        $this->actividades->method('findById')->willReturn($this->createMock(ActividadAll::class));
        $this->actividades->method('Guardar')->willReturn(false);
        $this->ingresos->expects($this->never())->method('Guardar');

        $this->assertFalse($uc->execute(['id_activ' => '3001'])['ok']);
    }
}

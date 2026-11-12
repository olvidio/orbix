<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasDlEdicion;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use Tests\support\DescribeCriteria;

/** Fila local de plazas de mi dl: se reutiliza, se rellena desde el calendario común o se crea. */
final class PlazasDlEdicionTest extends TestCase
{
    private ActividadPlazasDlRepositoryInterface&MockObject $local;
    private ActividadPlazasRepositoryInterface&MockObject $calendario;

    protected function setUp(): void
    {
        $this->local = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $this->calendario = $this->createMock(ActividadPlazasRepositoryInterface::class);
    }

    private function uc(): PlazasDlEdicion
    {
        return new PlazasDlEdicion($this->local, $this->calendario);
    }

    private function fila(?int $plazas, string $tabla = 'dlb', ?array $cedidas = null): ActividadPlazas
    {
        $p = new ActividadPlazas();
        $p->setId_activ(10);
        $p->setId_dl(7);
        $p->setDlTablaVo($tabla);
        $p->setPlazas($plazas);
        $p->setCedidas($cedidas);
        return $p;
    }

    public function test_fila_local_con_plazas_se_devuelve_sin_tocar(): void
    {
        $local = $this->fila(20);
        $this->local->method('matching')->willReturnCallback(function ($c) use ($local): array {
            $this->assertSame(['id_activ eq 10', 'id_dl eq 7', 'dl_tabla eq dlb'], DescribeCriteria::filtros($c));
            return [$local];
        });
        $this->calendario->expects($this->never())->method('matching');
        $this->local->expects($this->never())->method('Guardar');
        $this->assertSame($local, $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb'));
    }

    public function test_fila_local_vacia_se_rellena_del_calendario_sin_pisar_cedidas(): void
    {
        $local = $this->fila(null, cedidas: ['dlp' => 2]);
        $this->local->method('matching')->willReturn([$local]);
        $this->calendario->method('matching')->willReturn([$this->fila(30, cedidas: ['dlz' => 5])]);
        $this->local->expects($this->once())->method('Guardar')->with($local)->willReturn(true);
        $this->assertSame($local, $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb'));
        $this->assertSame('30', $local->getPlazas());
        $this->assertSame(['dlp' => 2], $local->getArrayCedidas());
    }

    public function test_fila_local_vacia_sin_cedidas_toma_las_del_calendario(): void
    {
        $local = $this->fila(0);
        $this->local->method('matching')->willReturn([$local]);
        $this->calendario->method('matching')->willReturn([$this->fila(30, cedidas: ['dlz' => 5])]);
        $this->local->method('Guardar')->willReturn(true);
        $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb');
        $this->assertSame(['dlz' => 5], $local->getArrayCedidas());
    }

    public function test_fila_local_vacia_si_no_se_guarda_devuelve_null(): void
    {
        $this->local->method('matching')->willReturn([$this->fila(null)]);
        $this->calendario->method('matching')->willReturn([$this->fila(30)]);
        $this->local->method('Guardar')->willReturn(false);
        $this->assertNull($this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb'));
    }

    public function test_fila_local_vacia_sin_calendario_se_devuelve_tal_cual(): void
    {
        $local = $this->fila(null);
        $this->local->method('matching')->willReturn([$local]);
        $this->calendario->method('matching')->willReturn([]);
        $this->local->expects($this->never())->method('Guardar');
        $this->assertSame($local, $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb'));
    }

    public function test_sin_fila_local_se_crea_desde_la_fila_del_calendario_de_mi_tabla(): void
    {
        $this->local->method('matching')->willReturn([]);
        $consultas = [];
        $this->calendario->method('matching')->willReturnCallback(function ($c) use (&$consultas): array {
            $consultas[] = DescribeCriteria::filtros($c);
            // Sin fila de mi tabla: la segunda consulta (sin dl_tabla) trae varias.
            return count($consultas) === 1 ? [] : [$this->fila(12, 'dlp'), $this->fila(30, 'dlb', ['dlz' => 1])];
        });
        $creada = null;
        $this->local->expects($this->once())->method('Guardar')->willReturnCallback(function (ActividadPlazas $p) use (&$creada): bool {
            $creada = $p;
            return true;
        });
        $res = $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb');
        $this->assertSame($creada, $res);
        $this->assertSame([['id_activ eq 10', 'id_dl eq 7', 'dl_tabla eq dlb'], ['id_activ eq 10', 'id_dl eq 7']], $consultas);
        $this->assertSame([10, 7, 'dlb', '30', ['dlz' => 1]], [$res->getId_activ(), $res->getId_dl(), $res->getDl_tabla(), $res->getPlazas(), $res->getArrayCedidas()]);
    }

    public function test_sin_fila_de_mi_tabla_usa_la_primera(): void
    {
        $this->local->method('matching')->willReturn([]);
        $this->calendario->method('matching')->willReturnOnConsecutiveCalls([], [$this->fila(12, 'dlp')]);
        $this->local->method('Guardar')->willReturn(true);
        $this->assertSame('12', $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb')->getPlazas());
    }

    public function test_sin_fila_local_ni_calendario_se_crea_vacia(): void
    {
        $this->local->method('matching')->willReturn([]);
        $this->calendario->method('matching')->willReturn([]);
        $this->local->method('Guardar')->willReturn(true);
        $res = $this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb');
        $this->assertNull($res->getPlazas());
        $this->assertSame('dlb', $res->getDl_tabla());
    }

    public function test_sin_fila_local_si_no_se_guarda_devuelve_null(): void
    {
        $this->local->method('matching')->willReturn([]);
        $this->calendario->method('matching')->willReturn([]);
        $this->local->method('Guardar')->willReturn(false);
        $this->assertNull($this->uc()->obtenerOCrearDesdeCalendario(10, 7, 'dlb'));
    }
}

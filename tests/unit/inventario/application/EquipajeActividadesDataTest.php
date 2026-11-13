<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\inventario\application\EquipajeActividadesData;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Actividades para los equipajes ({@see EquipajeActividadesData}): las de una casa en un periodo, el
 * resumen de las elegidas y los nombres de las de un equipaje (con aviso si una ya no existe).
 * Extiende myTest porque el nombre de la casa se resuelve con `Ubi::NewUbi()` (aquí, una casa inexistente).
 */
final class EquipajeActividadesDataTest extends myTest
{
    private const CASA = 10000001;

    /** @var list<string> */
    private array $filtros = [];

    private static function actividad(int $id, ?string $nom, string $ini, string $fin): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        if ($nom !== null) {
            $a->setNom_activ($nom);
        }
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        return $a;
    }

    private function uc(?string $idsActiv = '3001,3002,3009'): EquipajeActividadesData
    {
        $actividades = $this->createMock(ActividadRepositoryInterface::class);
        $actividades->method('getUbis')->willReturnCallback(function ($c): array {
            $this->filtros = DescribeCriteria::filtros($c);
            return [self::CASA, null, self::CASA + 1];
        });
        $actividades->method('matching')->willReturnCallback(function ($c): array {
            $this->filtros = DescribeCriteria::filtros($c);
            return [self::actividad(3001, 'crt A', '2026-07-01', '2026-07-05')];
        });
        $todas = $this->createMock(ActividadAllRepositoryInterface::class);
        $todas->method('findById')->willReturnCallback(static fn (int $id): ?ActividadAll => match ($id) {
            3001 => self::actividad(3001, 'crt A', '2026-07-10', '2026-07-15'),
            3002 => self::actividad(3002, 'crt B', '2026-07-01', '2026-07-05'),
            default => null,
        });
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(static function (int $id) use ($idsActiv): ?Equipaje {
            if ($id !== 5) {
                return null;
            }
            $e = new Equipaje();
            $e->setId_equipaje(5);
            $e->setIds_activ($idsActiv);
            return $e;
        });

        return new EquipajeActividadesData($actividades, $todas, $equipajes);
    }

    public function test_actividades_de_la_casa_en_el_periodo(): void
    {
        $r = $this->uc()->porPeriodo(self::CASA, '2026-07-01', '2026-07-31');

        $this->assertSame('id_ubi eq ' . self::CASA, $this->filtros[0]);
        $this->assertContains('f_ini lte 2026-07-31', $this->filtros);
        $this->assertContains('f_fin gte 2026-07-01', $this->filtros);
        $this->assertContains('status lt 4', $this->filtros);
        $this->assertSame(['sel' => ['id' => 3001, 'select' => ''], 1 => '1/7/2026', 2 => '5/7/2026', 3 => 'crt A', 4 => null], $r['a_valores'][1]);
        $this->assertSame('sin determinar', $r['nombre_ubi'], 'casa inexistente');
    }

    public function test_resumen_de_las_actividades_elegidas(): void
    {
        $r = $this->uc()->seleccion(['3001', 'x', '3002', '3009'], self::CASA);

        $this->assertSame(['1/7/2026', '15/7/2026', '3001,3002,3009'], [$r['ini'], $r['fin'], $r['ids_activ']], 'primera fecha de inicio y última de fin');
        $this->assertSame(['id_ubi_activ' => self::CASA], HashB::open($r['ctx_nuevo'], 'equipajes_nuevo_guardar'));
        $this->assertSame([false, false, ''], array_slice(array_values($this->uc()->seleccion([], self::CASA)), 1, 3));
    }

    public function test_nombres_de_las_actividades_de_un_equipaje_con_aviso_si_una_no_existe(): void
    {
        $this->assertSame(
            ['a_actividades' => [1 => 'crt A', 2 => 'crt B', 3 => 'OJO! No se encuentra la actividad con id: 3009']],
            $this->uc()->deEquipaje(5),
        );
        $this->assertSame(['a_actividades' => []], $this->uc(null)->deEquipaje(5), 'equipaje sin actividades');
        $this->assertSame([], $this->uc()->deEquipaje(6), 'equipaje inexistente');
    }

    public function test_casas_con_actividades_en_el_periodo(): void
    {
        $r = $this->uc()->casasConActividades('2026-07-01', '2026-07-31');

        $this->assertContains('f_ini lte 2026-07-31', $this->filtros);
        $this->assertContains('f_fin gte 2026-07-01', $this->filtros);
        $this->assertSame([self::CASA => 'sin determinar', self::CASA + 1 => 'sin determinar'], $r['a_opciones'], 'sin las actividades sin casa');
    }
}

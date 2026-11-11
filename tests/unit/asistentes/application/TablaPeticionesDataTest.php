<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\application\TablaPeticionesData;
use src\asistentes\domain\entity\Asistente;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Peticiones de plaza de los asistentes de una actividad: filas, partes con enlace de mover, vigencia y plazas. */
final class TablaPeticionesDataTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private AsistenteActividadService&MockObject $asistencias;
    private PlazaPeticionRepositoryInterface&MockObject $peticiones;
    private ResumenPlazasService&MockObject $resumen;
    private PersonaDlRepositoryInterface&MockObject $personas;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'oConfig', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
            unset($_SESSION[$k]);
        }
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->asistencias = $this->createMock(AsistenteActividadService::class);
        $this->peticiones = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function uc(): TablaPeticionesData
    {
        return new TablaPeticionesData($this->actividades, $this->asistencias, $this->peticiones, $this->resumen, $this->personas);
    }

    private function actividad(string $nombre, int $status = StatusId::ACTUAL, string $dl = 'dlp', int $tipo = 131001, ?string $ini = '2026-02-01'): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getStatus')->willReturn($status);
        $a->method('getDl_org')->willReturn($dl . 'f');
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getF_ini')->willReturn($ini === null ? null : new DateTimeLocal($ini));

        return $a;
    }

    private function asistente(int $idNom): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_nom')->willReturn($idNom);

        return $a;
    }

    private function persona(string $nombre, string $situacion = 'A'): PersonaDl
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getSituacion')->willReturn($situacion);
        $p->method('getApellidosNombre')->willReturn($nombre);

        return $p;
    }

    private function peticion(int $idActiv): PlazaPeticion
    {
        $p = $this->createMock(PlazaPeticion::class);
        $p->method('getId_activ')->willReturn($idActiv);

        return $p;
    }

    /** @param array<int, ActividadAll|null> $porId */
    private function actividadesPorId(array $porId): void
    {
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $porId[$id] ?? null);
    }

    public function test_el_nombre_y_el_id_salen_de_la_seleccion_o_de_la_actividad(): void
    {
        $this->actividadesPorId([3001 => $this->actividad('ca invierno')]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([]);

        $sel = $this->uc()->build(['sel' => ['3001#ca verano']]);
        $id = $this->uc()->build(['id_activ_old' => '3001']);

        $this->assertSame('ca verano', $sel['nom_activ']);
        $this->assertSame('ca invierno', $id['nom_activ']);
        $this->assertCount(2, $id['a_cabeceras']);
        $this->assertSame('src/asistentes/asistente_guardar', $id['paths']['asistente_guardar']);
    }

    public function test_una_actividad_inexistente_devuelve_la_tabla_vacia(): void
    {
        $this->actividadesPorId([]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);

        $this->assertSame([], $this->uc()->build(['id_activ_old' => '3001'])['a_valores']);
    }

    public function test_solo_cuentan_los_asistentes_en_alta(): void
    {
        $this->actividadesPorId([3001 => $this->actividad('ca')]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(1), $this->asistente(2), $this->asistente(3)]);
        $this->personas->method('findById')->willReturnMap([[1, $this->persona('Gil, Ana')], [2, null], [3, $this->persona('Paz, Eva', 'B')]]);
        $this->peticiones->method('matching')->willReturn([]);

        $r = $this->uc()->build(['id_activ_old' => '3001']);

        $this->assertSame([1], array_keys($r['a_valores']));
        $this->assertSame(['Gil, Ana', ''], [$r['a_valores'][1][1], $r['a_valores'][1][2]]);
    }

    public function test_las_peticiones_se_consultan_por_persona_y_tipo_de_actividad(): void
    {
        $this->actividadesPorId([3001 => $this->actividad('ca', StatusId::ACTUAL, 'dlp', 112001)]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $filtros = [];
        $this->peticiones->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($c);

            return [];
        });

        $this->uc()->build(['id_activ_old' => '3001']);

        $this->assertSame('id_nom eq 10011', $filtros[0]);
        $this->assertStringStartsWith('tipo matches ', $filtros[1]);
    }

    public function test_la_actividad_actual_va_como_texto_y_las_otras_como_enlace_de_mover_separadas_por_coma(): void
    {
        $this->actividadesPorId([
            3001 => $this->actividad('ca actual'),
            3002 => $this->actividad('ca otro', StatusId::ACTUAL, 'dlp'),
            3003 => $this->actividad('cancelado', 99),
            3004 => null,
        ]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $this->peticiones->method('matching')->willReturn([$this->peticion(3001), $this->peticion(0), $this->peticion(3003), $this->peticion(3004), $this->peticion(3002)]);

        $partes = $this->uc()->build(['id_activ_old' => '3001'])['a_valores'][1][2]['peticiones_parts'];

        $this->assertSame([['t' => 'p', 's' => 'ca actual'], ['t' => 'p', 's' => ', ']], array_slice($partes, 0, 2));
        $this->assertSame('m', $partes[2]['t']);
        $this->assertSame('ca otro', $partes[2]['s']);
        $this->assertSame(['mover', 10011, 3001, 3002, PlazaId::ASIGNADA, 't'], array_values(array_diff_key($partes[2]['h'], ['propietario' => 1])));
        $this->assertArrayNotHasKey('propietario', $partes[2]['h'], 'sin la app de plazas no hay propietario');
        $this->assertCount(3, $partes);
    }

    public function test_con_la_app_de_plazas_se_muestran_libres_concedidas_y_el_propietario_del_cupo(): void
    {
        $_SESSION['config'] = ['a_apps' => ['actividadplazas' => 4], 'app_installed' => [4]];
        $this->actividadesPorId([3001 => $this->actividad('ca actual'), 3002 => $this->actividad('ca otro', StatusId::ACTUAL, 'dlp')]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $this->peticiones->method('matching')->willReturn([$this->peticion(3002)]);
        $this->resumen->method('getPlazasConcedidasYLibres')->willReturn(['concedidas' => 10, 'libres' => 3]);

        $parte = $this->uc()->build(['id_activ_old' => '3001'])['a_valores'][1][2]['peticiones_parts'][0];

        $this->assertSame('ca otro (3/10)', $parte['s']);
        $this->assertSame('dlp>' . \src\shared\config\ConfigGlobal::mi_delef(), $parte['h']['propietario']);
    }

    public function test_sin_plazas_concedidas_el_nombre_va_sin_cifras(): void
    {
        $_SESSION['config'] = ['a_apps' => ['actividadplazas' => 4], 'app_installed' => [4]];
        $this->actividadesPorId([3001 => $this->actividad('ca actual'), 3002 => $this->actividad('ca otro')]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $this->peticiones->method('matching')->willReturn([$this->peticion(3002)]);
        $this->resumen->method('getPlazasConcedidasYLibres')->willReturn(['concedidas' => 0, 'libres' => 0]);

        $this->assertSame('ca otro', $this->uc()->build(['id_activ_old' => '3001'])['a_valores'][1][2]['peticiones_parts'][0]['s']);
    }

    public function test_el_estado_de_la_lista_solo_se_restaura_con_pila_valida(): void
    {
        $this->actividadesPorId([3001 => $this->actividad('ca')]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $this->peticiones->method('matching')->willReturn([]);
        $base = ['id_activ_old' => '3001', 'restored_id_sel' => '10011#x', 'restored_scroll_id' => '250'];

        $con = $this->uc()->build($base + ['stack' => '2'])['a_valores'];
        $cero = $this->uc()->build($base + ['stack' => '0'])['a_valores'];
        $sin = $this->uc()->build($base)['a_valores'];

        $this->assertSame(['10011#x', '250'], [$con['select'], $con['scroll_id']]);
        $this->assertArrayNotHasKey('select', $cero);
        $this->assertArrayNotHasKey('select', $sin);
    }

    public function test_una_actividad_fuera_del_curso_se_ignora_cuando_hay_configuracion(): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new \src\configuracion\domain\value_objects\ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $this->actividadesPorId([
            3001 => $this->actividad('ca actual', StatusId::ACTUAL, 'dlp', 132001),
            3002 => $this->actividad('ca antiguo', StatusId::ACTUAL, 'dlp', 132001, '2001-02-01'),
            3003 => $this->actividad('ca sin fecha', StatusId::ACTUAL, 'dlp', 132001, null),
        ]);
        $this->asistencias->method('getAsistentesDeActividad')->willReturn([$this->asistente(10011)]);
        $this->personas->method('findById')->willReturn($this->persona('Gil, Ana'));
        $this->peticiones->method('matching')->willReturn([$this->peticion(3002), $this->peticion(3003)]);

        $r = $this->uc()->build(['id_activ_old' => '3001']);

        $tipo = (new \src\actividades\domain\entity\TiposActividades(132001))->getActividadText();
        if (!in_array($tipo, ['ca', 'cv', 'crt'], true)) {
            $this->markTestSkipped("El tipo 132001 es «$tipo»: no filtra por curso.");
        }
        $this->assertSame('', $r['a_valores'][1][2], 'la antigua y la que no tiene fecha no son vigentes');
    }
}

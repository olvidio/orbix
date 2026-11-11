<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\casas\application\PrevisionAsistentesData;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\entity\Casa;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Previsión de asistentes: qué actividades ve cada oficina y qué plazas se muestran. */
final class PrevisionAsistentesDataTest extends myTest
{
    private ActividadDlRepositoryInterface&MockObject $actividades;
    private CasaDlRepositoryInterface&MockObject $casas;
    private IngresoRepositoryInterface&MockObject $ingresos;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<string> */
    private array $filtros = [];
    /** @var list<string> */
    private array $orden = [];
    /** @var list<ActividadAll> */
    private array $actividadesDevueltas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['session_auth', 'oConfig'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $_SESSION['session_auth']['sfsv'] = 1;
        $_SESSION['session_auth']['mi_oficina'] = '';
        $this->actividades = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros = DescribeCriteria::filtros($c);
            $this->orden = DescribeCriteria::orden($c);

            return $this->actividadesDevueltas;
        });
        $this->casas = $this->createMock(CasaDlRepositoryInterface::class);
        $this->ingresos = $this->createMock(IngresoRepositoryInterface::class);
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

    private function uc(): PrevisionAsistentesData
    {
        return new PrevisionAsistentesData($this->actividades, $this->casas, $this->ingresos);
    }

    private function gestionCalendario(string $gestion): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot($gestion, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
    }

    private function actividad(int $id, string $nombre, ?int $idUbi): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getId_ubi')->willReturn($idUbi);

        return $a;
    }

    /**
     * @dataProvider oficinasProvider
     */
    public function test_cada_oficina_ve_las_actividades_de_su_tipo(string $oficina, int $sfsv, string $patron): void
    {
        $_SESSION['session_auth']['sfsv'] = $sfsv;

        $r = $this->uc()->execute(['mi_of' => $oficina]);

        $this->assertTrue($r['permitido']);
        $this->assertSame($oficina, $r['mi_of']);
        $this->assertSame($sfsv, $r['mi_sfsv']);
        $this->assertSame(['id_tipo_activ matches ' . $patron], $this->filtros);
        $this->assertSame(['f_ini asc'], $this->orden);
    }

    /**
     * @return iterable<string, array{string, int, string}>
     */
    public static function oficinasProvider(): iterable
    {
        yield 'sm' => ['sm', 1, '^11'];
        yield 'nax' => ['nax', 1, '^12'];
        yield 'agd' => ['agd', 1, '^13'];
        yield 'sg' => ['sg', 1, '^1[45]'];
        yield 'sr' => ['sr', 1, '^17'];
        yield 'sm de sf' => ['sm', 2, '^21'];
        yield 'des (tipos fijos de sv)' => ['des', 2, '^(16|1141|1125|1341)'];
    }

    public function test_sin_oficina_en_la_entrada_se_usa_la_de_la_sesion(): void
    {
        $_SESSION['session_auth']['mi_oficina'] = 'agd';

        $r = $this->uc()->execute([]);

        $this->assertSame('agd', $r['mi_of']);
        $this->assertSame(['id_tipo_activ matches ^13'], $this->filtros);
    }

    public function test_otra_oficina_solo_entra_si_el_calendario_se_gestiona_de_forma_central(): void
    {
        $this->gestionCalendario('central');

        $r = $this->uc()->execute(['mi_of' => 'ocs']);

        $this->assertTrue($r['permitido']);
        $this->assertSame(['id_tipo_activ matches ^1'], $this->filtros);
    }

    public function test_otra_oficina_sin_gestion_central_no_esta_permitida_y_no_consulta_actividades(): void
    {
        $this->gestionCalendario('oficinas');
        $this->actividades->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['mi_of' => 'ocs', 'inicio_iso' => '2026-01-01', 'fin_iso' => '2026-12-31']);

        $this->assertFalse($r['permitido']);
        $this->assertSame([], $r['a_valores']);
        $this->assertCount(5, $r['a_cabeceras']);
    }

    public function test_sin_configuracion_en_la_sesion_otra_oficina_no_esta_permitida(): void
    {
        unset($_SESSION['oConfig']);

        $this->assertFalse($this->uc()->execute(['mi_of' => 'ocs'])['permitido']);
    }

    public function test_el_periodo_filtra_por_fecha_de_inicio_y_se_devuelve_en_formato_local(): void
    {
        $r = $this->uc()->execute(['mi_of' => 'sm', 'inicio_iso' => '2026-01-01', 'fin_iso' => '2026-12-31']);

        $this->assertSame(['f_ini between [2026-01-01,2026-12-31]', 'id_tipo_activ matches ^11'], $this->filtros);
        $this->assertSame('2026-01-01', $r['inicio_iso']);
        $this->assertSame('2026-12-31', $r['fin_iso']);
        $this->assertSame('1/1/2026', $r['inicio_local']);
        $this->assertSame('31/12/2026', $r['fin_local']);
    }

    public function test_desde_hoy_filtra_por_fecha_de_fin(): void
    {
        $this->uc()->execute(['mi_of' => 'sm', 'inicio_iso' => '2026-01-01', 'fin_iso' => '2026-12-31', 'periodo' => 'desdeHoy']);

        $this->assertContains('f_fin between [2026-01-01,2026-12-31]', $this->filtros);
    }

    public function test_con_solo_una_fecha_no_se_filtra_por_periodo(): void
    {
        $r = $this->uc()->execute(['mi_of' => 'sm', 'inicio_iso' => '2026-01-01']);

        $this->assertSame(['id_tipo_activ matches ^11'], $this->filtros);
        $this->assertSame('', $r['inicio_local']);
        $this->assertSame('', $r['fin_local']);
    }

    public function test_cada_actividad_lleva_las_plazas_de_la_casa_las_previstas_y_su_token_de_guardado(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, 'crt n', 77), $this->actividad(3002, 'ca n', 78)];
        $casa = $this->createMock(Casa::class);
        $casa->method('getPlazas')->willReturn(40);
        $casa->method('getPlazas_min')->willReturn(25);
        $this->casas->method('findById')->willReturnMap([[77, $casa], [78, null]]);
        $ingreso = new Ingreso();
        $ingreso->setId_activ(3001);
        $ingreso->setNumAsistentesPrevistosVo(32);
        $this->ingresos->method('findById')->willReturnMap([[3001, $ingreso], [3002, null]]);

        $r = $this->uc()->execute(['mi_of' => 'sm']);

        $this->assertSame([1, 2], array_keys($r['a_valores']));
        $fila = $r['a_valores'][1];
        $this->assertSame(3001, $fila['id']);
        $this->assertSame('tono2', $fila['clase']);
        $this->assertSame(['editable' => 'false', 'valor' => 'crt n'], $fila['actividad']);
        $this->assertSame(['editable' => 'false', 'valor' => 40], $fila['plazas']);
        $this->assertSame(['editable' => 'false', 'valor' => 25], $fila['plazas_min']);
        $this->assertSame(['editable' => 'true', 'valor' => 32], $fila['previstas']);
        $this->assertSame(['id_activ' => 3001], HashB::open($fila['ctx_update'], 'ingreso_plazas_previstas_update'));

        // Casa inexistente y actividad sin ingreso: ceros, sin fatal.
        $fila2 = $r['a_valores'][2];
        $this->assertSame(0, $fila2['plazas']['valor']);
        $this->assertSame(0, $fila2['plazas_min']['valor']);
        $this->assertSame(0, $fila2['previstas']['valor']);
    }

    public function test_una_actividad_sin_lugar_no_busca_la_casa(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, 'crt n', null)];
        $this->casas->expects($this->never())->method('findById');

        $r = $this->uc()->execute(['mi_of' => 'sm']);

        $this->assertSame(0, $r['a_valores'][1]['plazas']['valor']);
    }

    /**
     * Hallazgo: una casa sin plazas definidas deja `null` en la celda (no 0), a diferencia de la casa inexistente.
     */
    public function test_una_casa_sin_plazas_definidas_deja_null_en_la_celda(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001, 'crt n', 77)];
        $casa = $this->createMock(Casa::class);
        $casa->method('getPlazas')->willReturn(null);
        $casa->method('getPlazas_min')->willReturn(null);
        $this->casas->method('findById')->willReturn($casa);

        $r = $this->uc()->execute(['mi_of' => 'sm']);

        $this->assertNull($r['a_valores'][1]['plazas']['valor']);
        $this->assertNull($r['a_valores'][1]['plazas_min']['valor']);
    }
}

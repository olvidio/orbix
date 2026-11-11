<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asistentes\application\ListaUltimaActivData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\entity\PersonaS;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** «Quién no ha asistido todavía»: filtros de personas y actividades por tipo de lista, y filas del listado. */
final class ListaUltimaActivDataTest extends myTest
{
    private CentroDlRepositoryInterface&MockObject $centros;
    private ActividadDlRepositoryInterface&MockObject $actividadesDl;
    private ActividadRepositoryInterface&MockObject $actividades;
    private PersonaSRepositoryInterface&MockObject $personas;
    private AsistenteActividadService&MockObject $asistencias;
    private ActividadAllRepositoryInterface&MockObject $actividadAll;
    private mixed $configAnterior;

    /** @var list<string> */
    private array $filtrosPersonas = [];
    /** @var list<string> */
    private array $ordenPersonas = [];
    /** @var list<string> */
    private array $filtrosActividades = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, null, null, null, $curso, $curso);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->actividadesDl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('idsConFechaInicio')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosActividades = DescribeCriteria::filtros($c);

            return [3001 => '2025-10-01'];
        });
        $this->personas = $this->createMock(PersonaSRepositoryInterface::class);
        $this->personas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosPersonas = DescribeCriteria::filtros($c);
            $this->ordenPersonas = DescribeCriteria::orden($c);

            return $this->devueltas;
        });
        $this->asistencias = $this->createMock(AsistenteActividadService::class);
        $this->actividadAll = $this->createMock(ActividadAllRepositoryInterface::class);
    }

    /** @var list<PersonaS> */
    private array $devueltas = [];

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function uc(): ListaUltimaActivData
    {
        return new ListaUltimaActivData($this->centros, $this->actividadesDl, $this->actividades, $this->personas, $this->asistencias, $this->actividadAll);
    }

    private function persona(int $id, string $nombre, int $ctr = 1003): PersonaS&MockObject
    {
        $p = $this->createMock(PersonaS::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getId_ctr')->willReturn($ctr);

        return $p;
    }

    // ---- criterios por tipo de lista ----------------------------------------------------------------------

    /**
     * @dataProvider listasProvider
     */
    public function test_cada_lista_filtra_actividades_y_personas_a_su_manera(string $que, string $actividad, string $personaFiltro): void
    {
        $this->uc()->build(['que' => $que, 'curso' => 'actual']);

        $this->assertSame($actividad, $this->filtrosActividades[0] ?? '');
        $this->assertContains('situacion eq A', $this->filtrosPersonas);
        if ($personaFiltro !== '') {
            $this->assertNotSame([], preg_grep('/^' . preg_quote($personaFiltro, '/') . '/', $this->filtrosPersonas), $personaFiltro);
        }
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function listasProvider(): iterable
    {
        yield 'crt s o sg' => ['crt_s_sg', 'id_tipo_activ matches ^1[45]1', ''];
        yield 'crt s' => ['crt_s', 'id_tipo_activ matches ^141', ''];
        yield 'crt celadores' => ['crt_cel', 'id_tipo_activ matches ^141', 'eap matches_insensitive'];
        yield 'cv s' => ['cv_s', 'id_tipo_activ matches ^143', ''];
        yield 'cv s ad' => ['cv_s_ad', 'id_tipo_activ eq 1431', 'inc eq ad'];
        yield 'cv jóvenes' => ['cv_jovenes', '', 'f_nacimiento gt'];
    }

    public function test_los_s_no_celadores_incluyen_los_que_no_tienen_eap(): void
    {
        $this->uc()->build(['que' => 'crt_s']);

        $this->assertContains('(grupo)', $this->filtrosPersonas);
    }

    public function test_la_cv_para_jovenes_exige_no_tener_ce(): void
    {
        $this->uc()->build(['que' => 'cv_jovenes']);

        $this->assertContains('ce_ini is_null', $this->filtrosPersonas);
        $this->assertContains('ce_fin is_null', $this->filtrosPersonas);
    }

    public function test_con_un_centro_concreto_se_filtra_y_se_ordena_por_apellidos(): void
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centros->method('findById')->with(1003)->willReturn($c);

        $r = $this->uc()->build(['que' => 'crt_s_sg', 'id_ubi' => '1003']);

        $this->assertContains('id_ctr eq 1003', $this->filtrosPersonas);
        $this->assertSame(['apellido1 asc', 'apellido2 asc', 'nom asc'], $this->ordenPersonas);
        $this->assertStringContainsString('Centro A', $r['titulo']);
    }

    public function test_todos_los_centros_no_filtra_ni_ordena(): void
    {
        $this->uc()->build(['que' => 'crt_s_sg', 'id_ubi' => '999']);

        $this->assertNotContains('id_ctr eq 999', $this->filtrosPersonas);
        $this->assertSame([], $this->ordenPersonas);
    }

    public function test_el_curso_anterior_y_el_actual_dan_titulos_con_fechas_distintas(): void
    {
        $actual = $this->uc()->build(['que' => 'cv_s', 'curso' => 'actual'])['titulo'];
        $anterior = $this->uc()->build(['que' => 'cv_s', 'curso' => 'anterior'])['titulo'];
        $largo = $this->uc()->build(['que' => 'crt_cel'])['titulo'];

        $this->assertNotSame($actual, $anterior);
        $this->assertNotSame($actual, $largo);
    }

    public function test_los_avisos_de_celadores_y_de_ultima_cv_solo_salen_en_su_lista(): void
    {
        $this->assertStringContainsString('Eap', $this->uc()->build(['que' => 'crt_cel'])['alert_html']);
        $this->assertSame('', $this->uc()->build(['que' => 'cv_s'])['alert_html']);

        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('cv admisión');
        $a->method('getF_fin')->willReturn(new DateTimeLocal('2026-03-01'));
        $this->actividadesDl->method('matching')->willReturn([$a]);

        $this->assertStringContainsString('cv admisión', $this->uc()->build(['que' => 'cv_s_ad'])['alert_html']);
    }

    // ---- filas ----------------------------------------------------------------------

    private function asistencia(int $idActiv): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);

        return $a;
    }

    private function actividadConFecha(?string $ini): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn(141001);
        $a->method('getF_ini')->willReturn($ini === null ? null : new DateTimeLocal($ini));

        return $a;
    }

    public function test_quien_nunca_ha_asistido_sale_como_sin_datos_y_cuenta(): void
    {
        $this->devueltas = [$this->persona(10011, 'Gil, Ana'), $this->persona(10012, 'Paz, Eva')];
        $this->asistencias->method('getAsistenciasPersonaDeActividades')->willReturn([]);
        $this->centros->method('matching')->willReturn([]);

        $r = $this->uc()->build(['que' => 'cv_s']);

        $this->assertStringContainsString('2', $r['stats_html']);
        $this->assertStringContainsString('Gil, Ana', $r['tabla_html']);
        $this->assertStringContainsString('tono2', $r['tabla_html']);
    }

    public function test_quien_asistio_dentro_del_periodo_se_omite_y_fuera_se_lista_con_su_ultima_fecha(): void
    {
        $this->devueltas = [$this->persona(10011, 'Dentro, A'), $this->persona(10012, 'Fuera, B')];
        $this->asistencias->method('getAsistenciasPersonaDeActividades')->willReturnCallback(
            fn (int $id): array => [$this->asistencia($id === 10011 ? 3001 : 3002)]
        );
        $this->actividadAll->method('findById')->willReturnMap([
            [3001, $this->actividadConFecha((new \DateTimeImmutable('today'))->format('Y-m-d'))],
            [3002, $this->actividadConFecha('2001-05-04')],
        ]);
        $this->centros->method('matching')->willReturn([]);

        $r = $this->uc()->build(['que' => 'cv_s', 'curso' => 'actual']);

        $this->assertStringNotContainsString('Dentro, A', $r['tabla_html']);
        $this->assertStringContainsString('Fuera, B', $r['tabla_html']);
        $this->assertStringContainsString('4/5/2001', $r['tabla_html']);
    }

    public function test_una_asistencia_con_actividad_o_fecha_inexistente_se_lista_como_sin_datos(): void
    {
        $this->devueltas = [$this->persona(10011, 'Uno, A'), $this->persona(10012, 'Dos, B')];
        $this->asistencias->method('getAsistenciasPersonaDeActividades')->willReturnCallback(
            fn (int $id): array => [$this->asistencia($id === 10011 ? 3001 : 3002)]
        );
        $this->actividadAll->method('findById')->willReturnMap([[3001, null], [3002, $this->actividadConFecha(null)]]);
        $this->centros->method('matching')->willReturn([]);

        $r = $this->uc()->build(['que' => 'cv_s']);

        $this->assertGreaterThanOrEqual(2, substr_count($r['tabla_html'], _("No consta")));
        $this->assertStringContainsString('Uno, A', $r['tabla_html']);
        $this->assertStringContainsString('Dos, B', $r['tabla_html']);
    }

    public function test_sin_centro_elegido_cada_fila_muestra_el_nombre_del_centro_de_la_persona(): void
    {
        $this->devueltas = [$this->persona(10011, 'Gil, Ana', 1003)];
        $this->asistencias->method('getAsistenciasPersonaDeActividades')->willReturn([]);
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro Z');
        $this->centros->method('matching')->willReturn([$c]);

        $this->assertStringContainsString('Centro Z', $this->uc()->build(['que' => 'cv_s'])['tabla_html']);
    }
}

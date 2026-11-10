<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asistentes\application\ListaEstCtrData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Estudios en actividades por centro: centros por grupo, personas y qué se muestra de cada actividad. */
final class ListaEstCtrDataTest extends myTest
{
    private CentroDlRepositoryInterface&MockObject $centros;
    private PersonaDlRepositoryInterface&MockObject $personas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private MatriculaRepositoryInterface&MockObject $matriculas;
    private AsistenteActividadService&MockObject $asistencias;
    private AsistenteRepositoryInterface&MockObject $asistentes;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private mixed $configAnterior;
    /** @var list<string> */
    private array $filtrosCentros = [];
    /** @var list<string> */
    private array $filtrosPersonas = [];
    /** @var list<CentroDl> */
    private array $centrosDevueltos = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centros->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosCentros = DescribeCriteria::filtros($c);

            return $this->centrosDevueltos;
        });
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->asistencias = $this->createMock(AsistenteActividadService::class);
        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function uc(): ListaEstCtrData
    {
        return new ListaEstCtrData($this->centros, $this->personas, $this->actividades, $this->matriculas, $this->asistencias, $this->asistentes, $this->asignaturas);
    }

    private function centro(int $id, string $nombre, string $tipo = 'ndl'): CentroDl&MockObject
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);
        $c->method('getTipo_ctr')->willReturn($tipo);

        return $c;
    }

    private function persona(int $id, string $nombre, int $nivel = 1): PersonaDl&MockObject
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getNivelStgrVo')->willReturn(new NivelStgrId($nivel));

        return $p;
    }

    private function actividad(string $nombre, string $ini): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getF_ini')->willReturn(new DateTimeLocal($ini));

        return $a;
    }

    private function asistente(int $idActiv): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);

        return $a;
    }

    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '01/09/2025', 'empiezamax' => '31/08/2026'];

    /**
     * @dataProvider gruposProvider
     */
    public function test_cada_grupo_filtra_los_centros(string $grupo, string $esperado): void
    {
        $this->uc()->build(['n_agd' => $grupo, 'id_ubi' => '1003'] + self::PERIODO);

        $this->assertSame($esperado === '' ? [] : [$esperado], $this->filtrosCentros);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function gruposProvider(): iterable
    {
        yield 'agregados' => ['a', 'tipo_ctr matches ^a'];
        yield 'numerarios' => ['n', 'tipo_ctr matches ^n'];
        yield 'nm' => ['nm', 'tipo_ctr matches ^nm'];
        yield 'nj' => ['nj', 'tipo_ctr matches ^nj(ce)*'];
        yield 'un centro' => ['c', 'id_ubi eq 1003'];
        yield 'sin grupo' => ['', ''];
        // Hallazgo: `sssc` compara con `eq '^ss'` (igualdad literal): nunca devuelve centros.
        yield 'sssc (igualdad literal)' => ['sssc', 'tipo_ctr eq ^ss'];
    }

    public function test_los_centros_de_numerarios_excluyen_sacd_y_sin_estudios(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A', 'ndl'), $this->centro(1004, 'Centro B', 'agd')];
        $consultas = [];
        $this->personas->method('matching')->willReturnCallback(function (Criteria $c) use (&$consultas): array {
            $consultas[] = DescribeCriteria::filtros($c);

            return [];
        });

        $this->uc()->build(self::PERIODO);

        $this->assertContains('sacd eq false', $consultas[0]);
        $this->assertContains('nivel_stgr neq ' . NivelStgrId::N, $consultas[0]);
        $this->assertNotContains('sacd eq false', $consultas[1]);
        $this->assertContains('id_ctr eq 1004', $consultas[1]);
    }

    public function test_sin_centros_se_devuelve_html_de_lista_vacio_con_pie(): void
    {
        $r = $this->uc()->build(self::PERIODO);

        $this->assertSame(['lista_html'], array_keys($r));
        $this->assertIsString($r['lista_html']);
    }

    private function conPersonaYActividad(int $nivel, string $inicio): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personas->method('matching')->willReturn([$this->persona(10011, 'Gil, Ana', $nivel)]);
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([$this->asistente(3001)]);
        $this->actividades->method('findById')->willReturn($this->actividad('ca invierno', $inicio));
    }

    public function test_una_actividad_ya_pasada_se_marca_como_ya_hecha_sin_asignaturas(): void
    {
        $this->conPersonaYActividad(1, '2001-01-10');
        $this->matriculas->expects($this->never())->method('matching');

        $html = $this->uc()->build(self::PERIODO)['lista_html'];

        $this->assertStringContainsString(_("ya lo ha hecho"), $html);
        $this->assertStringNotContainsString('ca invierno', $html);
    }

    /**
     * @dataProvider nivelesProvider
     */
    public function test_una_actividad_futura_muestra_el_plan_de_formacion_o_el_repaso(int $nivel, string $texto): void
    {
        $this->conPersonaYActividad($nivel, '2999-01-10');
        $this->matriculas->expects($this->never())->method('matching');

        $html = $this->uc()->build(self::PERIODO)['lista_html'];

        $this->assertStringContainsString('ca invierno', $html);
        $this->assertStringContainsString($texto, $html);
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function nivelesProvider(): iterable
    {
        yield 'sin estudios' => [NivelStgrId::N, 'plan de formación'];
        yield 'repaso' => [NivelStgrId::R, 'repaso'];
    }

    public function test_para_el_resto_de_niveles_se_listan_las_asignaturas_matriculadas_con_sus_creditos(): void
    {
        $this->conPersonaYActividad(1, '2999-01-10');
        $m = $this->createMock(Matricula::class);
        $m->method('getId_asignatura')->willReturn(1101);
        $m->method('isPreceptor')->willReturn(false);
        $this->matriculas->method('matching')->willReturn([$m]);
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_corto')->willReturn('Fil');
        $a->method('getCreditos')->willReturn(4.5);
        $this->asignaturas->method('findById')->with(1101)->willReturn($a);

        $html = $this->uc()->build(self::PERIODO)['lista_html'];

        $this->assertStringContainsString('Fil (4.5', $html);
    }

    public function test_una_matricula_de_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->conPersonaYActividad(1, '2999-01-10');
        $m = $this->createMock(Matricula::class);
        $m->method('getId_asignatura')->willReturn(1101);
        $this->matriculas->method('matching')->willReturn([$m]);
        $this->asignaturas->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->uc()->build(self::PERIODO);
    }

    public function test_un_preceptor_sin_id_se_marca_solo_con_p(): void
    {
        $this->conPersonaYActividad(1, '2999-01-10');
        $m = $this->createMock(Matricula::class);
        $m->method('getId_asignatura')->willReturn(1101);
        $m->method('isPreceptor')->willReturn(true);
        $m->method('getId_preceptor')->willReturn(null);
        $this->matriculas->method('matching')->willReturn([$m]);
        $a = $this->createMock(Asignatura::class);
        $a->method('getNombre_corto')->willReturn('Fil');
        $a->method('getCreditos')->willReturn(4.0);
        $this->asignaturas->method('findById')->willReturn($a);

        $this->assertStringContainsString('(p)', $this->uc()->build(self::PERIODO)['lista_html']);
    }

    public function test_si_la_actividad_no_existe_la_persona_solo_lleva_numero_y_nombre(): void
    {
        $this->centrosDevueltos = [$this->centro(1003, 'Centro A')];
        $this->personas->method('matching')->willReturn([$this->persona(10011, 'Gil, Ana')]);
        $this->asistencias->method('getActividadesDeAsistente')->willReturn([$this->asistente(3001)]);
        $this->actividades->method('findById')->willReturn(null);

        $html = $this->uc()->build(self::PERIODO)['lista_html'];

        $this->assertStringContainsString('Gil, Ana', $html);
    }
}

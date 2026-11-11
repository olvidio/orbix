<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\MatriculasListaData;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;
use Tests\support\DescribeCriteria;

/** Lista de matrículas de las actividades que empiezan en un periodo, con preceptor, nota y correos. */
final class MatriculasListaDataTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const PERIODO = ['inicio_iso' => '2026-06-01', 'fin_iso' => '2026-09-30'];

    private ActividadRepositoryInterface&MockObject $actividadesPeriodo;
    private MatriculaDlRepositoryInterface&MockObject $matriculas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private TelecoPersonaService&MockObject $telecos;
    private PersonaFinderService&MockObject $finder;
    /** @var array<int, string> id_nom => «apellidos, nombre» */
    private array $personas = [];
    /** @var array<int, string> id_nom => correos */
    private array $correos = [];
    /** @var list<Matricula> */
    private array $matriculasDevueltas = [];
    /** @var list<string> */
    private array $filtrosActividades = [];
    /** @var list<string> */
    private array $filtrosMatriculas = [];
    /** @var list<int> */
    private array $personasBuscadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            $this->personasBuscadas[] = $id;
            if (!isset($this->personas[$id])) {
                return null;
            }
            $p = $this->createMock(PersonaDl::class);
            $p->method('getPrefApellidosNombre')->willReturn($this->personas[$id]);
            $p->method('getCentro_o_dl')->willReturn('ctr' . $id);
            $p->method('getDl')->willReturn('dlb');

            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $this->finder);

        $this->actividadesPeriodo = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividadesPeriodo->method('idsConFechaInicio')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosActividades = DescribeCriteria::filtros($c);

            return ['2026-07-01#1' => 3001, '2026-08-01#1' => 3002];
        });
        $this->matriculas = $this->createMock(MatriculaDlRepositoryInterface::class);
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosMatriculas = DescribeCriteria::filtros($c);

            return $this->matriculasDevueltas;
        });
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn([1101 => 'Lógica', 1102 => 'Metafísica', 1103 => 'Antropología'][$id] ?? 'Asig ' . $id);

            return $a;
        });
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actividades->method('findById')->willReturnCallback(function (int $id): ?ActividadAll {
            if ($id === 3999) {
                return null;
            }
            $a = $this->createMock(ActividadAll::class);
            $a->method('getNom_activ')->willReturn('ca ' . $id);

            return $a;
        });
        $this->telecos = $this->createMock(TelecoPersonaService::class);
        $this->telecos->method('getTelecosPorTipo')->willReturnCallback(
            fn (int $idNom, string $tipo, string $sep): string => $tipo === 'e-mail' && $sep === ' / ' ? ($this->correos[$idNom] ?? '') : 'mal'
        );
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): MatriculasListaData
    {
        return new MatriculasListaData($this->actividadesPeriodo, $this->matriculas, $this->asignaturas, $this->actividades, $this->telecos);
    }

    private function matricula(int $idActiv, int $idAsignatura, int $idNom, ?bool $preceptor = null, ?int $idPreceptor = null, ?float $nota = null, ?int $max = null): void
    {
        $m = new Matricula();
        $m->setId_activ($idActiv);
        $m->setId_asignatura($idAsignatura);
        $m->setId_nom($idNom);
        $m->setPreceptor($preceptor);
        $m->setId_preceptor($idPreceptor);
        $m->setNotaNumVo($nota);
        $m->setNotaMaxVo($max);
        $this->matriculasDevueltas[] = $m;
    }

    public function test_sin_matriculas_devuelve_solo_el_titulo_con_el_periodo(): void
    {
        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame(['f_ini between [2026-06-01,2026-09-30]'], $this->filtrosActividades);
        $this->assertSame(['id_activ in [3001,3002]'], $this->filtrosMatriculas);
        $this->assertStringContainsString('1/6/2026', $r['titulo']);
        $this->assertStringContainsString('30/9/2026', $r['titulo']);
        $this->assertSame('', $r['msg_err']);
        $this->assertSame([], $r['a_valores']);
    }

    public function test_fila_completa_con_preceptor_nota_y_correos(): void
    {
        $this->personas = [10011 => 'Pla, Joan', 20001 => 'Vidal, Pere'];
        $this->correos = [10011 => 'joan@example.org', 20001 => 'pere@example.org / p2@example.org'];
        $this->matricula(3001, 1101, 10011, true, 20001, 8.5, 10);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame('', $r['msg_err']);
        $this->assertSame([[
            'sel' => '3001#1101#10011',
            1 => 'Pla, Joan [joan@example.org]',
            2 => 'ctr10011',
            3 => 'dlb',
            4 => 'ca 3001',
            5 => 'Lógica',
            6 => 'Vidal, Pere [pere@example.org / p2@example.org]',
            7 => '8.5 [10]',
        ]], $r['a_valores']);
    }

    public function test_sin_preceptor_ni_nota_ni_correos_las_celdas_quedan_vacias(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10011);

        $fila = $this->uc()->execute(self::PERIODO)['a_valores'][0];

        $this->assertSame('Pla, Joan', $fila[1]);
        $this->assertSame('', $fila[6]);
        $this->assertSame('', $fila[7]);
    }

    public function test_con_preceptor_sin_asignar_se_marca_con_una_x(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10011, true);

        $this->assertSame('x', $this->uc()->execute(self::PERIODO)['a_valores'][0][6]);
    }

    public function test_un_preceptor_que_no_se_encuentra_se_marca_con_una_x_y_se_avisa(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10011, true, 20009);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame('x', $r['a_valores'][0][6]);
        $this->assertStringContainsString('preceptor: No encuentro a nadie con id_nom: 20009', $r['msg_err']);
    }

    public function test_una_nota_sin_maximo_deja_los_corchetes_vacios(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10011, null, null, 7.0);

        $this->assertSame('7 []', $this->uc()->execute(self::PERIODO)['a_valores'][0][7]);
    }

    public function test_las_filas_se_ordenan_por_alumno_y_asignatura(): void
    {
        $this->personas = [10011 => 'Pla, Joan', 10012 => 'Bosch, Pau'];
        $this->matricula(3001, 1102, 10011);
        $this->matricula(3001, 1101, 10011);
        $this->matricula(3002, 1101, 10012);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame([0, 1, 2], array_keys($r['a_valores']));
        $this->assertSame(
            ['3002#1101#10012', '3001#1101#10011', '3001#1102#10011'],
            array_column($r['a_valores'], 'sel')
        );
    }

    public function test_las_matriculas_seguidas_del_mismo_alumno_solo_lo_buscan_una_vez(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10011);
        $this->matricula(3001, 1102, 10011);
        $this->matricula(3001, 1103, 10011);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertCount(3, $r['a_valores']);
        $this->assertSame([10011], $this->personasBuscadas);
    }

    public function test_un_alumno_que_no_se_encuentra_se_omite_y_se_avisa(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 1101, 10019);
        $this->matricula(3001, 1101, 10011);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame(['3001#1101#10011'], array_column($r['a_valores'], 'sel'));
        $this->assertStringContainsString('id_nom: 10019', $r['msg_err']);
    }

    public function test_una_matricula_de_una_actividad_que_no_se_encuentra_se_omite_y_se_avisa(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3999, 1101, 10011);
        $this->matricula(3001, 1101, 10011);

        $r = $this->uc()->execute(self::PERIODO);

        $this->assertSame(['3001#1101#10011'], array_column($r['a_valores'], 'sel'));
        $this->assertStringContainsString('3999', $r['msg_err']);
    }

    /** Hallazgo: una matrícula de una asignatura que ya no existe lanza `RuntimeException` y tumba el listado. */
    public function test_una_asignatura_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->personas = [10011 => 'Pla, Joan'];
        $this->matricula(3001, 9999, 10011);

        $this->expectException(\RuntimeException::class);

        $this->uc()->execute(self::PERIODO);
    }

    /** Hallazgo: los mensajes de error llevan la ruta del fichero PHP del servidor. */
    public function test_el_aviso_de_persona_no_encontrada_incluye_la_ruta_del_fichero(): void
    {
        $this->matricula(3001, 1101, 10019);

        $this->assertStringContainsString('MatriculasListaData.php', $this->uc()->execute(self::PERIODO)['msg_err']);
    }
}

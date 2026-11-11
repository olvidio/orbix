<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\entity\Cargo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\application\PlanEstudiosCaData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\unit\actividadestudios\application\support\SustituyeServiciosDelContenedor;
use Tests\unit\planning\support\DescribeCriteria;

/** Plan de estudios de un ca: director de estudios, profesores y preceptores, y las asignaturas de cada alumno. */
final class PlanEstudiosCaDataTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;
    private const ID_CARGO_DEST = 7;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private CargoRepositoryInterface&MockObject $cargos;
    private ActividadCargoRepositoryInterface&MockObject $actividadCargos;
    private ActividadAsignaturaDlRepositoryInterface&MockObject $actividadAsignaturas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private AsistenteActividadService&MockObject $asistentesSvc;
    private MatriculaRepositoryInterface&MockObject $matriculas;
    /** @var array<int, array{string, ?int}> id_nom => [nombre, nivel stgr] */
    private array $personas = [];
    /** @var array<string, list<string>> */
    private array $filtros = [];
    /** @var list<ActividadCargo> */
    private array $directores = [];
    /** @var list<ActividadAsignatura> */
    private array $asignaturasDelCa = [];
    /** @var list<Asistente> */
    private array $asistentes = [];
    /** @var array<int, list<array{int, ?bool}>> id_nom => [[id_asignatura, preceptor], …] */
    private array $matriculasPorAlumno = [];

    public function setUp(): void
    {
        parent::setUp();
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if (!isset($this->personas[$id])) {
                return null;
            }
            $p = $this->createMock(PersonaDl::class);
            $p->method('getPrefApellidosNombre')->willReturn($this->personas[$id][0]);
            $p->method('getCentro_o_dl')->willReturn('ctr' . $id);
            $p->method('getNivel_stgr')->willReturn($this->personas[$id][1]);

            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $cargo = $this->createMock(Cargo::class);
        $cargo->method('getId_cargo')->willReturn(self::ID_CARGO_DEST);
        $this->cargos->method('matching')->willReturnCallback(function (Criteria $c) use ($cargo): array {
            $this->filtros['cargos'] = DescribeCriteria::filtros($c);

            return [$cargo];
        });
        $this->actividadCargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividadCargos->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['directores'] = DescribeCriteria::filtros($c);

            return $this->directores;
        });
        $this->actividadAsignaturas = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->actividadAsignaturas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['asignaturas'] = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->asignaturasDelCa;
        });
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn('Asig ' . $id);
            $a->method('getCreditos')->willReturn(4.5);

            return $a;
        });
        $this->asistentesSvc = $this->createMock(AsistenteActividadService::class);
        $this->asistentesSvc->method('getAsistentesDeActividad')->with(self::ID_ACTIV)
            ->willReturnCallback(fn (): array => $this->asistentes);
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $this->filtros['matriculas'] = $f;
            $lista = [];
            foreach ($this->matriculasPorAlumno[(int) substr($f[0], strlen('id_nom eq '))] ?? [] as [$idAsignatura, $preceptor]) {
                $m = $this->createMock(Matricula::class);
                $m->method('getId_asignatura')->willReturn($idAsignatura);
                $m->method('isPreceptor')->willReturn($preceptor);
                $lista[] = $m;
            }

            return $lista;
        });
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): PlanEstudiosCaData
    {
        return new PlanEstudiosCaData(
            $this->actividades,
            $this->cargos,
            $this->actividadCargos,
            $this->actividadAsignaturas,
            $this->asignaturas,
            $this->asistentesSvc,
            $this->matriculas,
        );
    }

    private function actividad(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('ca n verano');
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    private function director(int $idNom): void
    {
        $c = $this->createMock(ActividadCargo::class);
        $c->method('getId_nom')->willReturn($idNom);
        $this->directores[] = $c;
    }

    private function asignaturaDelCa(int $idAsignatura, ?int $idProfesor = null, ?string $tipo = null): void
    {
        $a = $this->createMock(ActividadAsignatura::class);
        $a->method('getId_asignatura')->willReturn($idAsignatura);
        $a->method('getId_profesor')->willReturn($idProfesor);
        $a->method('getTipo')->willReturn($tipo);
        $this->asignaturasDelCa[] = $a;
    }

    private function asistente(int $idNom, bool $propio = true, ?string $observEst = null): void
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_nom')->willReturn($idNom);
        $a->method('isPropio')->willReturn($propio);
        $a->method('getObserv_est')->willReturn($observEst);
        $this->asistentes[] = $a;
    }

    public function test_actividad_no_encontrada(): void
    {
        $this->actividades->method('findById')->willReturn(null);
        $this->cargos->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_activ' => '3001']);

        $this->assertStringContainsString('3001', $r['msg_err']);
        $this->assertSame(['', '', [], [], []], [$r['nom_activ'], $r['nom_director_est'], $r['aPreceptores'], $r['aProfesores'], $r['aAlumnos']]);
    }

    public function test_el_director_de_estudios_es_quien_tiene_el_cargo_dest(): void
    {
        $this->actividad();
        $this->director(10011);
        $this->personas[10011] = ['Pla Roca, Joan', null];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['cargo eq d.est.'], $this->filtros['cargos']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_cargo eq ' . self::ID_CARGO_DEST], $this->filtros['directores']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'tipo asc'], $this->filtros['asignaturas']);
        $this->assertSame('ca n verano', $r['nom_activ']);
        $this->assertSame('Pla Roca, Joan', $r['nom_director_est']);
        $this->assertSame('', $r['msg_err']);
    }

    public function test_sin_director_nombrado_se_indica_como_nombrarlo(): void
    {
        $this->actividad();

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertNotSame('', $r['nom_director_est']);
        $this->assertStringNotContainsString('<', $r['nom_director_est']);
    }

    public function test_un_director_que_no_se_encuentra_deja_el_nombre_vacio_y_avisa(): void
    {
        $this->actividad();
        $this->director(10011);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame('', $r['nom_director_est']);
        $this->assertStringContainsString('id_nom: 10011', $r['msg_err']);
    }

    /** Hallazgo: igual que en la lista de clases, sin el cargo «d.est.» en la tabla de cargos hay error fatal. */
    public function test_sin_el_cargo_dest_en_la_tabla_de_cargos_revienta(): void
    {
        $this->actividad();
        $cargos = $this->createMock(CargoRepositoryInterface::class);
        $cargos->method('matching')->willReturn([]);
        $uc = new PlanEstudiosCaData($this->actividades, $cargos, $this->actividadCargos, $this->actividadAsignaturas, $this->asignaturas, $this->asistentesSvc, $this->matriculas);

        $this->expectException(\Error::class);

        @$uc->execute(['id_activ' => self::ID_ACTIV]);
    }

    public function test_las_asignaturas_se_reparten_entre_profesores_y_preceptores(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101, 20001);
        $this->asignaturaDelCa(1102, 20002, 'p');
        $this->asignaturaDelCa(1103);
        $this->personas += [20001 => ['Vidal, Pere', null], 20002 => ['Soler, Marc', null]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([
            1 => ['nombre_corto' => 'Asig 1101', 'creditos' => 4.5, 'nom_profesor' => 'Vidal, Pere'],
            3 => ['nombre_corto' => 'Asig 1103', 'creditos' => 4.5, 'nom_profesor' => '?'],
        ], $r['aProfesores']);
        $this->assertSame([2 => ['nombre_corto' => 'Asig 1102', 'creditos' => 4.5, 'nom_profesor' => 'Soler, Marc']], $r['aPreceptores']);
    }

    /**
     * Hallazgo: si el profesor asignado no se encuentra, la asignatura desaparece del plan (solo queda el aviso);
     * la lista de clases, en cambio, la muestra con el profesor en blanco.
     */
    public function test_una_asignatura_cuyo_profesor_no_se_encuentra_desaparece_del_plan(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101, 20009);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([], $r['aProfesores']);
        $this->assertStringContainsString('id_nom: 20009', $r['msg_err']);
    }

    public function test_una_asignatura_del_ca_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(9999);

        $this->expectException(\RuntimeException::class);

        $this->uc()->execute(['id_activ' => self::ID_ACTIV]);
    }

    public function test_cada_alumno_propio_lleva_sus_asignaturas_matriculadas(): void
    {
        $this->actividad();
        $this->asistente(30001, true, 'llega el día 3');
        $this->asistente(30002, false);
        $this->personas += [30001 => ['Zamora, Luis', NivelStgrId::C1], 30002 => ['Otro, De Fuera', NivelStgrId::C1]];
        $this->matriculasPorAlumno[30001] = [[1101, null], [1102, true]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['id_nom eq 30001', 'id_activ eq ' . self::ID_ACTIV], $this->filtros['matriculas']);
        $this->assertSame([1], array_keys($r['aAlumnos']));
        $alumno = $r['aAlumnos'][1];
        $this->assertSame(['Zamora, Luis', 'ctr30001', 'llega el día 3'], [$alumno['nom_persona'], $alumno['ctr'], $alumno['observ_est']]);
        $this->assertSame(['nombre_corto' => 'Asig 1101', 'creditos' => 4.5, 'preceptor' => ''], $alumno['aAsignaturas'][1]);
        $this->assertSame('Asig 1102', $alumno['aAsignaturas'][2]['nombre_corto']);
        $this->assertMatchesRegularExpression('/^\(.+\)$/', $alumno['aAsignaturas'][2]['preceptor']);
    }

    /**
     * @dataProvider sinMatriculasProvider
     */
    public function test_un_alumno_sin_matriculas_lleva_el_texto_de_su_situacion(?int $nivel, bool $interrogantes): void
    {
        $this->actividad();
        $this->asistente(30001);
        $this->personas[30001] = ['Zamora, Luis', $nivel];

        $texto = $this->uc()->execute(['id_activ' => self::ID_ACTIV])['aAlumnos'][1]['aAsignaturas'];

        $this->assertIsString($texto);
        $this->assertSame($interrogantes, $texto === '???');
    }

    /**
     * @return iterable<string, array{?int, bool}>
     */
    public static function sinMatriculasProvider(): iterable
    {
        yield 'repaso' => [NivelStgrId::R, false];
        yield 'sin estudios (plan de formación)' => [NivelStgrId::N, false];
        yield 'cuadrienio sin matricular' => [NivelStgrId::C1, true];
        yield 'sin nivel' => [null, true];
    }

    public function test_repaso_y_plan_de_formacion_tienen_textos_distintos(): void
    {
        $this->actividad();
        $this->asistente(30001);
        $this->asistente(30002);
        $this->personas += [30001 => ['Zamora, Luis', NivelStgrId::R], 30002 => ['Bosch, Pau', NivelStgrId::N]];

        $alumnos = $this->uc()->execute(['id_activ' => self::ID_ACTIV])['aAlumnos'];

        $this->assertNotSame($alumnos[1]['aAsignaturas'], $alumnos[2]['aAsignaturas']);
    }

    /** Hallazgo: el índice se incrementa antes de buscar a la persona; un alumno no encontrado deja un hueco. */
    public function test_un_alumno_que_no_se_encuentra_se_omite_se_avisa_y_deja_un_hueco_en_la_numeracion(): void
    {
        $this->actividad();
        $this->asistente(30009);
        $this->asistente(30001);
        $this->personas[30001] = ['Zamora, Luis', NivelStgrId::R];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([2], array_keys($r['aAlumnos']));
        $this->assertStringContainsString('id_nom: 30009', $r['msg_err']);
    }

    public function test_una_matricula_de_una_asignatura_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->actividad();
        $this->asistente(30001);
        $this->personas[30001] = ['Zamora, Luis', NivelStgrId::C1];
        $this->matriculasPorAlumno[30001] = [[9999, null]];

        $this->expectException(\RuntimeException::class);

        $this->uc()->execute(['id_activ' => self::ID_ACTIV]);
    }
}

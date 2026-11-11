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
use src\actividadestudios\application\ListaClasesCaData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\unit\actividadestudios\application\support\SustituyeServiciosDelContenedor;
use Tests\unit\planning\support\DescribeCriteria;

/** Lista de clases de un ca: director de estudios, profesor de cada asignatura y alumnos matriculados. */
final class ListaClasesCaDataTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;
    private const ID_CARGO_DEST = 7;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private CargoRepositoryInterface&MockObject $cargos;
    private ActividadCargoRepositoryInterface&MockObject $actividadCargos;
    private ActividadAsignaturaRepositoryInterface&MockObject $actividadAsignaturas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private MatriculaRepositoryInterface&MockObject $matriculas;
    /** @var array<int, string> id_nom => «apellidos, nombre»; los que no están no se encuentran */
    private array $personas = [];
    /** @var array<string, list<string>> */
    private array $filtros = [];
    /** @var list<ActividadCargo> */
    private array $directores = [];
    /** @var list<ActividadAsignatura> */
    private array $asignaturasDelCa = [];
    /** @var array<int, list<int>> id_asignatura => ids de los matriculados */
    private array $matriculados = [];

    public function setUp(): void
    {
        parent::setUp();
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if (!isset($this->personas[$id])) {
                return null;
            }
            $p = $this->createMock(PersonaDl::class);
            $p->method('getPrefApellidosNombre')->willReturn($this->personas[$id]);
            $p->method('getCentro_o_dl')->willReturn('ctr' . $id);

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
        $this->actividadAsignaturas = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $this->actividadAsignaturas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['asignaturas'] = DescribeCriteria::filtros($c);

            return $this->asignaturasDelCa;
        });
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn('Asig ' . $id);

            return $a;
        });
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $this->filtros['matriculas'] = $f;
            $lista = [];
            foreach ($this->matriculados[(int) substr($f[1], strlen('id_asignatura eq '))] ?? [] as $idNom) {
                $m = $this->createMock(Matricula::class);
                $m->method('getId_nom')->willReturn($idNom);
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

    private function uc(): ListaClasesCaData
    {
        return new ListaClasesCaData(
            $this->actividades,
            $this->cargos,
            $this->actividadCargos,
            $this->actividadAsignaturas,
            $this->asignaturas,
            $this->matriculas,
        );
    }

    private function actividad(?string $dlOrg = null): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('ca n verano');
        $a->method('getDl_org')->willReturn($dlOrg ?? ConfigGlobal::mi_delef());
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

    public function test_actividad_no_encontrada(): void
    {
        $this->actividades->method('findById')->willReturn(null);
        $this->cargos->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_activ' => '3001']);

        $this->assertStringContainsString('3001', $r['msg_err']);
        $this->assertSame('', $r['nom_activ']);
        $this->assertSame('', $r['nom_director_est']);
        $this->assertSame([], $r['datos_asignatura']);
    }

    public function test_el_director_de_estudios_es_quien_tiene_el_cargo_dest_en_la_actividad(): void
    {
        $this->actividad();
        $this->director(10011);
        $this->director(10012);
        $this->personas[10011] = 'Pla Roca, Joan';

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['cargo eq d.est.'], $this->filtros['cargos']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_cargo eq ' . self::ID_CARGO_DEST], $this->filtros['directores']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV], $this->filtros['asignaturas']);
        $this->assertSame('', $r['msg_err']);
        $this->assertSame('ca n verano', $r['nom_activ']);
        $this->assertSame('Pla Roca, Joan', $r['nom_director_est']);
        $this->assertSame([], $r['datos_asignatura']);
    }

    /** Hallazgo: el caso de uso devuelve HTML (`<span class=no_print>`) en el nombre del director. */
    public function test_sin_director_nombrado_se_devuelve_la_indicacion_de_como_nombrarlo(): void
    {
        $this->actividad();

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertStringStartsWith('<span class=no_print>', $r['nom_director_est']);
        $this->assertSame('', $r['msg_err']);
    }

    public function test_un_director_que_no_se_encuentra_deja_el_nombre_vacio_y_avisa(): void
    {
        $this->actividad();
        $this->director(10011);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame('', $r['nom_director_est']);
        $this->assertStringContainsString('id_nom: 10011', $r['msg_err']);
    }

    /**
     * Hallazgo: si el cargo «d.est.» no está en la tabla de cargos, `$cCargos[0]` no existe y la pantalla acaba
     * en un error fatal en lugar de un mensaje.
     */
    public function test_sin_el_cargo_dest_en_la_tabla_de_cargos_el_listado_revienta(): void
    {
        $this->actividad();
        $cargos = $this->createMock(CargoRepositoryInterface::class);
        $cargos->method('matching')->willReturn([]);
        $uc = new ListaClasesCaData($this->actividades, $cargos, $this->actividadCargos, $this->actividadAsignaturas, $this->asignaturas, $this->matriculas);

        $this->expectException(\Error::class);

        @$uc->execute(['id_activ' => self::ID_ACTIV]);
    }

    public function test_cada_asignatura_lleva_su_profesor_y_sus_alumnos_ordenados_sin_acentos(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101, 20001);
        $this->asignaturaDelCa(1102, 20002, 'p');
        $this->asignaturaDelCa(1103);
        $this->personas += [
            20001 => 'Vidal, Pere',
            20002 => 'Soler, Marc',
            30001 => 'Zamora, Luis',
            30002 => 'Álvarez, Juan',
            30003 => 'Blanco, Pau',
        ];
        $this->matriculados = [1101 => [30001, 30002, 30003], 1102 => [30003]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame('', $r['msg_err']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_asignatura eq 1103'], $this->filtros['matriculas']);
        $this->assertSame([1, 2, 3], array_keys($r['datos_asignatura']));
        $primera = $r['datos_asignatura'][1];
        $this->assertSame('Vidal, Pere', $primera['nom_profesor']);
        $this->assertSame('Profesor', $primera['tipo_profesor']);
        $this->assertSame('Asig 1101', $primera['nombre_corto']);
        $this->assertSame(
            ['Álvarez, Juan' => 'ctr30002', 'Blanco, Pau' => 'ctr30003', 'Zamora, Luis' => 'ctr30001'],
            $primera['alumnos']
        );
        $this->assertSame('Preceptor', $r['datos_asignatura'][2]['tipo_profesor']);
        $this->assertSame('Soler, Marc', $r['datos_asignatura'][2]['nom_profesor']);
        $this->assertSame(['nom_profesor' => '', 'tipo_profesor' => 'Profesor', 'nombre_corto' => 'Asig 1103', 'alumnos' => []], $r['datos_asignatura'][3]);
    }

    public function test_un_profesor_que_no_se_encuentra_deja_el_nombre_vacio_y_avisa(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101, 20001);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame('', $r['datos_asignatura'][1]['nom_profesor']);
        $this->assertStringContainsString('id_nom: 20001 (profesor)', $r['msg_err']);
    }

    public function test_un_alumno_que_no_se_encuentra_se_omite_y_se_avisa_si_la_actividad_es_de_mi_dl(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101);
        $this->personas[30001] = 'Zamora, Luis';
        $this->matriculados = [1101 => [30001, 30009]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['Zamora, Luis' => 'ctr30001'], $r['datos_asignatura'][1]['alumnos']);
        $this->assertStringContainsString('id_nom: 30009', $r['msg_err']);
    }

    public function test_en_una_actividad_de_otra_dl_los_alumnos_que_no_se_encuentran_se_omiten_sin_avisar(): void
    {
        $this->actividad('otra');
        $this->asignaturaDelCa(1101);
        $this->matriculados = [1101 => [30009]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([], $r['datos_asignatura'][1]['alumnos']);
        $this->assertSame('', $r['msg_err']);
    }

    /** Hallazgo: una asignatura del ca que ya no existe lanza `RuntimeException` y tumba el listado entero. */
    public function test_una_asignatura_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(9999);

        $this->expectException(\RuntimeException::class);

        $this->uc()->execute(['id_activ' => self::ID_ACTIV]);
    }

    /** Hallazgo: los alumnos se indexan por nombre; dos con el mismo «apellidos, nombre» quedan en una sola fila. */
    public function test_dos_alumnos_con_el_mismo_nombre_quedan_en_una_sola_fila(): void
    {
        $this->actividad();
        $this->asignaturaDelCa(1101);
        $this->personas += [30001 => 'García, José', 30002 => 'García, José'];
        $this->matriculados = [1101 => [30001, 30002]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['García, José' => 'ctr30002'], $r['datos_asignatura'][1]['alumnos']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\application\PosiblesAsignaturasCaData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\unit\actividadestudios\application\support\SustituyeServiciosDelContenedor;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Asignaturas posibles para un ca: cuántos asistentes en estudios podrían cursar cada asignatura y a quiénes les
 * queda poco para acabar el cuadrienio.
 *
 * Las asignaturas que faltan se calculan con `AsignaturasPendientes` (SQL sobre una tabla temporal que se llena con
 * las asignaturas del repositorio simulado y sobre `e_notas`): los ids de persona del test no existen en la BD, así
 * que no tienen notas y les faltan todas las asignaturas del cuadrienio que haya en el repositorio.
 */
final class PosiblesAsignaturasCaDataTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;

    private AsistenteRepositoryInterface&MockObject $asistentes;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    /** @var array<int, array{string, ?int}> id_nom => [nombre, nivel stgr] */
    private array $personas = [];
    /** @var list<Asignatura> */
    private array $catalogo = [];
    /** @var array<int, array<int, int>> id_nom => [id_asignatura => id_situacion] */
    private array $notasPorPersona = [];
    /** @var list<string> */
    private array $filtrosAsistentes = [];
    /** @var list<string> */
    private array $filtrosNotas = [];
    /** @var list<list<string>> */
    private array $consultasAsignaturas = [];

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
            $p->method('getNivel_stgr')->willReturn($this->personas[$id][1]);

            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->asistentes->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosAsistentes = DescribeCriteria::filtros($c);
            $lista = [];
            foreach (array_keys($this->personas) as $idNom) {
                $a = $this->createMock(Asistente::class);
                $a->method('getId_nom')->willReturn($idNom);
                $lista[] = $a;
            }
            $fantasma = $this->createMock(Asistente::class);
            $fantasma->method('getId_nom')->willReturn(-999000999);
            $lista[] = $fantasma;

            return $lista;
        });
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosNotas = DescribeCriteria::filtros($c);
            $idNom = (int) substr($this->filtrosNotas[0], strlen('id_nom eq '));
            $lista = [];
            foreach ($this->notasPorPersona[$idNom] ?? [] as $idAsignatura => $idSituacion) {
                $n = $this->createMock(PersonaNota::class);
                $n->method('getId_asignatura')->willReturn($idAsignatura);
                $n->method('getId_situacion')->willReturn($idSituacion);
                $lista[] = $n;
            }

            return $lista;
        });
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultasAsignaturas[] = array_merge($f, DescribeCriteria::orden($c));
            foreach ($f as $filtro) {
                // Tramo del cuadrienio que pide `AsignaturasPendientes`.
                if (str_starts_with($filtro, 'id_nivel between')) {
                    return array_values(array_filter(
                        $this->catalogo,
                        static fn (Asignatura $a): bool => $a->getId_nivel() >= 2100 && $a->getId_nivel() <= 2500
                    ));
                }
            }

            return $this->catalogo;
        });
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): PosiblesAsignaturasCaData
    {
        return new PosiblesAsignaturasCaData($this->asistentes, $this->notas, $this->asignaturas);
    }

    private function asignatura(int $id, int $nivel, string $nombre): void
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_asignatura')->willReturn($id);
        $a->method('getId_nivel')->willReturn($nivel);
        $a->method('getNombre_asignatura')->willReturn($nombre);
        $a->method('getNombre_corto')->willReturn(mb_substr($nombre, 0, 20));
        $a->method('isActive')->willReturn(true);
        $a->method('getId_tipo')->willReturn(1);
        $this->catalogo[] = $a;
    }

    /** Dos asignaturas de bienio y $n de cuadrienio. */
    private function catalogoConCuadrienio(int $n): void
    {
        $this->asignatura(1101, 1101, 'Lógica');
        $this->asignatura(1102, 1102, 'Metafísica');
        for ($i = 1; $i <= $n; $i++) {
            $this->asignatura(2100 + $i, 2100 + $i, 'Teología ' . $i);
        }
    }

    public function test_solo_cuentan_los_asistentes_en_bienio_o_cuadrienio(): void
    {
        $this->catalogoConCuadrienio(6);
        $this->personas = [
            -999000001 => ['Pla, Joan', NivelStgrId::C1],
            -999000002 => ['Roca, Pere', NivelStgrId::R],
            -999000003 => ['Soler, Marc', null],
            -999000004 => ['Vidal, Lluc', NivelStgrId::B],
        ];

        $r = $this->uc()->execute(['id_activ' => (string) self::ID_ACTIV, 'nom_activ' => 'ca n verano']);

        $this->assertSame(['id_activ eq ' . self::ID_ACTIV], $this->filtrosAsistentes);
        $this->assertSame('ca n verano', $r['nom_activ']);
        $this->assertCount(8, $r['aAsignaturas_alumnos']);
        foreach ($r['aAsignaturas_alumnos'] as $fila) {
            $this->assertSame(2, $fila['posibles_alumnos']);
            $this->assertSame(['Pla, Joan', 'Vidal, Lluc'], $fila['aNombresAlumnos']);
        }
        $this->assertSame(
            ['nom_asignatura' => 'Lógica', 'id_asignatura' => 1101, 'posibles_alumnos' => 2, 'aNombresAlumnos' => ['Pla, Joan', 'Vidal, Lluc']],
            $r['aAsignaturas_alumnos'][0]
        );
    }

    public function test_las_asignaturas_se_piden_de_los_dos_planes_activas_sin_opcionales_genericas_y_por_nivel(): void
    {
        $this->catalogoConCuadrienio(1);
        $this->personas = [-999000001 => ['Pla, Joan', NivelStgrId::C1]];

        $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $delCasoDeUso = array_values(array_filter(
            $this->consultasAsignaturas,
            static fn (array $f): bool => in_array('id_nivel asc', $f, true)
        ));
        $this->assertCount(2, $delCasoDeUso);
        $this->assertStringContainsString('1997', $delCasoDeUso[0][0]);
        $this->assertStringContainsString('2026', $delCasoDeUso[1][0]);
        $this->assertSame(['active eq true', 'id_tipo neq 8', 'id_nivel asc'], array_slice($delCasoDeUso[0], 1));
        $this->assertSame(['id_nom eq -999000001', 'id_nivel between [1100,2500]'], $this->filtrosNotas);
    }

    public function test_una_asignatura_con_nota_no_es_posible_para_ese_alumno_y_si_nadie_puede_cursarla_no_sale(): void
    {
        $this->catalogoConCuadrienio(1);
        $this->personas = [
            -999000001 => ['Pla, Joan', NivelStgrId::C1],
            -999000004 => ['Vidal, Lluc', NivelStgrId::B],
        ];
        $this->notasPorPersona = [
            -999000001 => [1101 => 10, 1102 => 10],
            -999000004 => [1101 => 10],
        ];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([1102, 2101], array_column($r['aAsignaturas_alumnos'], 'id_asignatura'));
        $this->assertSame(['Vidal, Lluc'], $r['aAsignaturas_alumnos'][0]['aNombresAlumnos']);
        $this->assertSame(2, $r['aAsignaturas_alumnos'][1]['posibles_alumnos']);
    }

    /**
     * Hallazgo: basta que exista una nota de la asignatura, sea cual sea su situación (aquí 2, «cursada» sin
     * superar): la asignatura deja de ser posible para ese alumno.
     */
    public function test_cualquier_nota_de_la_asignatura_la_descarta_aunque_no_este_superada(): void
    {
        $this->asignatura(1101, 1101, 'Lógica');
        $this->personas = [-999000001 => ['Pla, Joan', NivelStgrId::B]];
        $this->notasPorPersona = [-999000001 => [1101 => 2]];

        $this->assertSame([], $this->uc()->execute(['id_activ' => self::ID_ACTIV])['aAsignaturas_alumnos']);
    }

    public function test_los_alumnos_salen_por_orden_alfabetico(): void
    {
        $this->asignatura(1101, 1101, 'Lógica');
        $this->personas = [
            -999000001 => ['Vidal, Lluc', NivelStgrId::B],
            -999000002 => ['Bosch, Pau', NivelStgrId::B],
        ];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['Bosch, Pau', 'Vidal, Lluc'], $r['aAsignaturas_alumnos'][0]['aNombresAlumnos']);
    }

    public function test_a_quien_le_faltan_menos_de_cinco_asignaturas_del_cuadrienio_sale_en_la_lista_de_fin_de_cuadrienio(): void
    {
        $this->catalogoConCuadrienio(4);
        $this->personas = [-999000001 => ['Pla, Joan', NivelStgrId::C2]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertCount(1, $r['a_alumnos_fin_c']);
        $this->assertSame('Pla, Joan', $r['a_alumnos_fin_c'][0]['apellidos_nombre']);
        $this->assertEqualsCanonicalizing(
            ['Teología 1', 'Teología 2', 'Teología 3', 'Teología 4'],
            $r['a_alumnos_fin_c'][0]['asignaturas']
        );
    }

    public function test_con_cinco_o_mas_asignaturas_pendientes_no_sale_en_la_lista_de_fin_de_cuadrienio(): void
    {
        $this->catalogoConCuadrienio(5);
        $this->personas = [-999000001 => ['Pla, Joan', NivelStgrId::C2]];

        $this->assertSame([], $this->uc()->execute(['id_activ' => self::ID_ACTIV])['a_alumnos_fin_c']);
    }

    /**
     * Hallazgo: la lista de fin de cuadrienio no mira el nivel del alumno: uno de bienio sale también (con las
     * asignaturas de cuadrienio que «le faltan»), y si el catálogo no tiene cuadrienio sale con la lista vacía.
     */
    public function test_un_alumno_de_bienio_tambien_sale_en_la_lista_de_fin_de_cuadrienio(): void
    {
        $this->asignatura(1101, 1101, 'Lógica');
        $this->personas = [-999000001 => ['Pla, Joan', NivelStgrId::B]];

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame([['apellidos_nombre' => 'Pla, Joan', 'asignaturas' => []]], $r['a_alumnos_fin_c']);
    }

    public function test_sin_asistentes_en_estudios_no_hay_asignaturas_posibles(): void
    {
        $this->catalogoConCuadrienio(2);

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV]);

        $this->assertSame(['nom_activ' => '', 'aAsignaturas_alumnos' => [], 'a_alumnos_fin_c' => []], $r);
    }
}

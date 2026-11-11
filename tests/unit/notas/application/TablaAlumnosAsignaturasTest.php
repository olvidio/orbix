<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\NivelId;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\AsignaturasPendientesData;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\TablaAlumnosAsignaturas;
use src\notas\application\Tesera;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Matriz alumnos × asignaturas de «asignaturas pendientes» y su pantalla de datos. */
final class TablaAlumnosAsignaturasTest extends myTest
{
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaDlRepositoryInterface&MockObject $personas;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private mixed $configAnterior;

    /** @var list<PersonaNota> */
    private array $notasDeTodos = [];
    /** @var list<Criteria> */
    private array $criteriosPersonas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);

        // Catálogo: sin filtros → todas; con filtros (plan, activas, rango) → las del plan.
        $catalogo = [$this->asig(1101, 11010, 'Fil', 4.0), $this->asig(1102, 11020, 'Teo', null), $this->asig(2101, 21010, 'Mor', 6.0)];
        $this->asignaturas->method('matching')->willReturnCallback(
            fn (Criteria $c): array => $catalogo
        );
        // El plan de la persona se resuelve con una consulta por id_asignatura 9998; el resto son las notas.
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            foreach (DescribeCriteria::filtros($c) as $f) {
                if (str_starts_with($f, 'id_asignatura eq')) {
                    return [];
                }
            }

            return $this->notasDeTodos;
        });
        $this->personas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->criteriosPersonas[] = $c;

            return $this->personasDevueltas;
        });
    }

    /** @var list<PersonaDl> */
    private array $personasDevueltas = [];

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function asig(int $idNivel, int $idAsignatura, string $corto, ?float $creditos): Asignatura&MockObject
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_nivel')->willReturn($idNivel);
        $a->method('getId_asignatura')->willReturn($idAsignatura);
        $a->method('getNombre_corto')->willReturn($corto);
        $a->method('getCreditos')->willReturn($creditos);

        return $a;
    }

    private function persona(int $idNom, string $nombre = 'Gil, Ana'): PersonaDl&MockObject
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn($idNom);
        $p->method('getId_tabla')->willReturn('n');
        $p->method('getNivel_stgr')->willReturn(1);
        $p->method('getDl')->willReturn('dlb');
        $p->method('getCentro_o_dl')->willReturn('Centro Z');
        $p->method('getPrefApellidosNombre')->willReturn($nombre);

        return $p;
    }

    private function nota(int $idNom, int $idAsignatura, int $idNivel, int $idSituacion): PersonaNota&MockObject
    {
        $n = $this->createMock(PersonaNota::class);
        $n->method('getId_nom')->willReturn($idNom);
        $n->method('getId_asignatura')->willReturn($idAsignatura);
        $n->method('getIdNivelVo')->willReturn(NivelId::fromNullableInt($idNivel));
        $n->method('getId_situacion')->willReturn($idSituacion);

        return $n;
    }

    private function tabla(): TablaAlumnosAsignaturas
    {
        $tesera = new Tesera(new ExpedienteNotasPersona($this->notas), $this->asignaturas, new PlanEstudiosDePersona($this->notas));

        return new TablaAlumnosAsignaturas($this->asignaturas, $this->personas, $this->notas, $tesera);
    }

    private function conAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, null, null, null, ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8], null);
    }

    // ---- cabeceras y filas ----------------------------------------------------------------------

    public function test_las_cabeceras_llevan_cuatro_fijas_y_una_columna_por_asignatura_con_sus_creditos(): void
    {
        $r = $this->tabla()->paraDelegacion();

        $this->assertCount(7, $r['cabeceras']);
        $this->assertSame(['Fil (4)', 'Teo ()', 'Mor (6)'], array_slice($r['cabeceras'], 4, 3, false));
        $this->assertSame([], $r['filas']);
    }

    public function test_sin_notas_toda_asignatura_del_plan_esta_pendiente(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];

        $fila = $this->tabla()->paraDelegacion()['filas'][1];

        $this->assertSame('n', $fila[1]);
        $this->assertSame(1, $fila[2]);
        $this->assertSame('Centro Z', $fila[3]);
        $this->assertSame('Gil, Ana', $fila[4]);
        $this->assertSame([5 => TablaAlumnosAsignaturas::CELDA_PENDIENTE, 6 => 1, 7 => 1], array_slice($fila, 4, 3, true));
    }

    public function test_cursada_aprobada_y_pendiente_se_distinguen_por_celda(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];
        $this->notasDeTodos = [
            $this->nota(10011, 11010, 1101, NotaSituacion::CURSADA),
            $this->nota(10011, 11020, 1102, NotaSituacion::SUPERADA),
        ];

        $fila = $this->tabla()->paraDelegacion()['filas'][1];

        $this->assertSame(TablaAlumnosAsignaturas::CELDA_CURSADA, $fila[5]);
        $this->assertSame(TablaAlumnosAsignaturas::CELDA_APROBADA, $fila[6]);
        $this->assertSame(TablaAlumnosAsignaturas::CELDA_PENDIENTE, $fila[7]);
    }

    public function test_una_nota_no_superada_ni_cursada_deja_la_asignatura_pendiente(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];
        $this->notasDeTodos = [$this->nota(10011, 11010, 1101, NotaSituacion::NO_HECHA_CA)];

        $this->assertSame(TablaAlumnosAsignaturas::CELDA_PENDIENTE, $this->tabla()->paraDelegacion()['filas'][1][5]);
    }

    public function test_las_notas_de_otra_persona_no_cuentan(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];
        $this->notasDeTodos = [$this->nota(99999, 11010, 1101, NotaSituacion::SUPERADA)];

        $this->assertSame(TablaAlumnosAsignaturas::CELDA_PENDIENTE, $this->tabla()->paraDelegacion()['filas'][1][5]);
    }

    public function test_fin_de_bienio_blanquea_solo_las_asignaturas_de_bienio(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];
        $this->notasDeTodos = [$this->nota(10011, 9999, 9999, NotaSituacion::SUPERADA)];

        $fila = $this->tabla()->paraDelegacion()['filas'][1];

        $this->assertSame('', $fila[5]);
        $this->assertSame('', $fila[6]);
        $this->assertSame(TablaAlumnosAsignaturas::CELDA_PENDIENTE, $fila[7], 'el cuadrienio sigue pendiente');
    }

    public function test_fin_de_cuadrienio_blanquea_todo(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];
        $this->notasDeTodos = [$this->nota(10011, 9998, 9998, NotaSituacion::SUPERADA)];

        $fila = $this->tabla()->paraDelegacion()['filas'][1];

        $this->assertSame(['', '', ''], [$fila[5], $fila[6], $fila[7]]);
    }

    public function test_cada_persona_tiene_su_fila_numerada_en_orden(): void
    {
        $this->personasDevueltas = [$this->persona(10011, 'Gil, Ana'), $this->persona(10012, 'Paz, Eva')];

        $filas = $this->tabla()->paraDelegacion()['filas'];

        $this->assertSame([1, 2], array_keys($filas));
        $this->assertSame('Paz, Eva', $filas[2][4]);
    }

    // ---- criterios y variantes ----------------------------------------------------------------------

    public function test_delegacion_busca_personas_activas_de_nivel_stgr_b_c1_c2_y_bc(): void
    {
        $this->tabla()->paraDelegacion();

        $c = $this->criteriosPersonas[0];
        $filtros = DescribeCriteria::filtros($c);
        $this->assertSame('situacion eq A', $filtros[0]);
        $this->assertStringStartsWith('nivel_stgr in', $filtros[1]);
        $this->assertSame(['nivel_stgr', 'apellido1', 'nom'], array_map(fn ($o) => explode(' ', $o)[0], DescribeCriteria::orden($c)));
    }

    public function test_region_stgr_sin_delegaciones_validas_no_consulta_personas(): void
    {
        $r = $this->tabla()->paraRegionStgr([5, 6], [9 => 'dlz']);

        $this->assertSame([], $r['filas']);
        $this->assertCount(4, $r['cabeceras']);
        $this->assertSame([], $this->criteriosPersonas);
    }

    public function test_region_stgr_filtra_por_las_dl_elegidas_y_muestra_la_dl_como_centro(): void
    {
        $this->personasDevueltas = [$this->persona(10011)];

        $r = $this->tabla()->paraRegionStgr([9, 5], [9 => 'dlz', 8 => 'dly']);

        $this->assertStringContainsString('dl in [dlz]', implode('|', DescribeCriteria::filtros($this->criteriosPersonas[0])));
        // nivel_stgr es numérico: se filtra por lista de niveles, no por regex de texto.
        $this->assertStringStartsWith('nivel_stgr in', DescribeCriteria::filtros($this->criteriosPersonas[0])[1]);
        $this->assertSame('dlb', $r['filas'][1][3]);
        $this->assertSame('dl', explode(' ', DescribeCriteria::orden($this->criteriosPersonas[0])[0])[0]);
    }

    // ---- AsignaturasPendientesData ----------------------------------------------------------------------

    public function test_pantalla_en_ambito_dl_devuelve_la_tabla_sin_delegaciones(): void
    {
        $this->conAmbito('dl');
        $this->personasDevueltas = [$this->persona(10011)];
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->expects($this->never())->method('getArrayDlRegionStgr');

        $r = (new AsignaturasPendientesData($delegaciones, $this->tabla()))->execute(['dl' => ['9']]);

        $this->assertFalse($r['ambito_rstgr']);
        $this->assertSame([], $r['delegaciones']);
        $this->assertCount(1, $r['filas']);
    }

    public function test_pantalla_en_rstgr_sin_seleccion_devuelve_solo_las_delegaciones(): void
    {
        $this->conAmbito('rstgr');
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->method('getArrayDlRegionStgr')->willReturn([9 => 'dlz']);

        $r = (new AsignaturasPendientesData($delegaciones, $this->tabla()))->execute(['dl' => ['', [], null], 'otro' => 1]);

        $this->assertTrue($r['ambito_rstgr']);
        $this->assertSame([9 => 'dlz'], $r['delegaciones']);
        $this->assertSame([], $r['cabeceras']);
        $this->assertSame([], $r['filas']);
    }

    public function test_pantalla_en_rstgr_con_seleccion_construye_la_tabla_de_esas_dl(): void
    {
        $this->conAmbito('rstgr');
        $this->personasDevueltas = [$this->persona(10011)];
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->method('getArrayDlRegionStgr')->willReturn([9 => 'dlz']);

        $r = (new AsignaturasPendientesData($delegaciones, $this->tabla()))->execute(['dl' => ['9']]);

        $this->assertTrue($r['ambito_rstgr']);
        $this->assertSame('dlb', $r['filas'][1][3]);
        $this->assertSame([9 => 'dlz'], $r['delegaciones']);
    }
}

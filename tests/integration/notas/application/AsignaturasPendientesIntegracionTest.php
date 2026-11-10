<?php

declare(strict_types=1);

namespace Tests\integration\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\AsignaturasPendientes;
use src\notas\domain\value_objects\CursoStgr;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Consultas de «asignaturas pendientes» contra la BD real (solo lectura): el SQL es lo importante
 * de la clase y no se puede probar con mocks. Los datos se descubren en la BD y las aserciones son
 * relaciones que deben cumplirse con cualquier contenido.
 */
final class AsignaturasPendientesIntegracionTest extends myTest
{
    private AsignaturaRepositoryInterface $asignaturas;
    private \PDO $pdo;

    public function setUp(): void
    {
        parent::setUp();
        $this->asignaturas = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $this->pdo = GlobalPdo::get('oDB');
    }

    private function notas(): string
    {
        return ConfigGlobal::mi_sfsv() == 2 ? 'publicf.e_notas' : 'publicv.e_notas';
    }

    // ---- guardas ----------------------------------------------------------------------

    public function test_sin_asignatura_valida_no_consulta(): void
    {
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');

        $this->assertSame([], $uc->personasQueLesFaltaAsignatura(0, CursoStgr::BIENIO, 0));
        $this->assertSame([], $uc->personasQueLesFaltaAsignatura(-1, CursoStgr::BIENIO, 0));
    }

    public function test_sin_tabla_de_personas_las_consultas_lanzan_excepcion(): void
    {
        $uc = new AsignaturasPendientes($this->asignaturas);

        foreach ([
            fn () => $uc->personasQueLesFaltaAsignatura(1101, CursoStgr::BIENIO, 0),
            fn () => $uc->contarFaltantesPorPersona(2, CursoStgr::BIENIO),
            fn () => $uc->listarFaltantesPorPersona(2, CursoStgr::BIENIO),
        ] as $llamada) {
            try {
                $llamada();
                $this->fail('debía lanzar RuntimeException');
            } catch (\RuntimeException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    // ---- personas a las que les falta una asignatura ----------------------------------------------------------------------

    public function test_quien_tiene_nota_de_la_asignatura_no_figura_entre_los_que_les_falta(): void
    {
        $fila = $this->pdo->query(
            "SELECT n.id_nom, n.id_asignatura FROM {$this->notas()} n
             JOIN p_numerarios p ON p.id_nom = n.id_nom
             WHERE p.situacion = 'A' AND p.nivel_stgr IN (1,2,3,4) AND n.id_asignatura BETWEEN 1000 AND 2999 LIMIT 1"
        )->fetch(\PDO::FETCH_ASSOC);
        if ($fila === false) {
            $this->markTestSkipped('No hay un numerario activo con notas en la BD.');
        }
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');

        $faltan = $uc->personasQueLesFaltaAsignatura((int) $fila['id_asignatura'], CursoStgr::CUADRIENIO, 0);

        $this->assertArrayNotHasKey((int) $fila['id_nom'], $faltan);
        foreach ($faltan as $idNom => $marca) {
            $this->assertIsInt($idNom);
            $this->assertSame(1, $marca);
        }
    }

    public function test_una_asignatura_sin_notas_les_falta_a_todos_los_activos_del_tramo(): void
    {
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');
        $activos = $this->pdo->query(
            "SELECT count(*) FROM p_numerarios WHERE situacion = 'A' AND nivel_stgr IN (" . implode(',', CursoStgr::BIENIO->nivelesStgr()) . ')'
        )->fetchColumn();

        // 99999 no es ninguna asignatura: nadie la tiene aprobada.
        $faltan = $uc->personasQueLesFaltaAsignatura(99999, CursoStgr::BIENIO, 0);

        $this->assertCount((int) $activos, $faltan);
    }

    // ---- faltantes por persona ----------------------------------------------------------------------

    public function test_contar_y_listar_devuelven_las_mismas_personas_dentro_del_maximo(): void
    {
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');

        $conteo = $uc->contarFaltantesPorPersona(2, CursoStgr::CUADRIENIO);
        $lista = $uc->listarFaltantesPorPersona(2, CursoStgr::CUADRIENIO);

        $this->assertEqualsCanonicalizing(array_keys($conteo), array_keys($lista));
        foreach ($conteo as $idNom => $faltan) {
            $this->assertGreaterThanOrEqual(0, $faltan, "persona $idNom");
            $this->assertLessThanOrEqual(2, $faltan, "persona $idNom");
        }
        foreach ($lista as $nombres) {
            $this->assertContainsOnly('string', $nombres);
        }
    }

    public function test_una_persona_inexistente_debe_todas_las_asignaturas_del_tramo(): void
    {
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');

        $faltan = $uc->asignaturasQueFaltanPersona(999999999, CursoStgr::BIENIO);

        $this->assertNotSame([], $faltan);
        $this->assertContainsOnly('string', $faltan);
        $this->assertSame(array_values(array_unique($faltan)), $faltan, 'sin duplicados');
    }

    public function test_las_asignaturas_con_nota_no_figuran_entre_las_que_faltan(): void
    {
        $filas = $this->pdo->query(
            "SELECT id_nom, id_asignatura FROM {$this->notas()} WHERE id_asignatura BETWEEN 1000 AND 2999 LIMIT 50"
        )->fetchAll(\PDO::FETCH_ASSOC);
        $fila = null;
        foreach ($filas as $candidata) {
            $asig = $this->asignaturas->findById((int) $candidata['id_asignatura']);
            if ($asig !== null && $asig->isActive() && (int) $asig->getId_tipo() !== 8) {
                $fila = ['id_nom' => $candidata['id_nom'], 'nombre_corto' => (string) $asig->getNombre_corto()];
                break;
            }
        }
        if ($fila === null) {
            $this->markTestSkipped('No hay notas de asignaturas obligatorias activas en la BD.');
        }
        $uc = new AsignaturasPendientes($this->asignaturas, 'p_numerarios');

        $faltan = $uc->asignaturasQueFaltanPersona((int) $fila['id_nom'], CursoStgr::CUADRIENIO);
        $faltanBienio = $uc->asignaturasQueFaltanPersona((int) $fila['id_nom'], CursoStgr::BIENIO);

        $this->assertNotContains($fila['nombre_corto'], array_merge($faltan, $faltanBienio));
    }
}

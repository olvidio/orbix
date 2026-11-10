<?php

declare(strict_types=1);

namespace Tests\integration\notas\application;

use src\asignaturas\domain\entity\Asignatura;
use src\notas\application\services\ResumenTempTablesService;
use src\notas\application\support\ResumenFactory;
use src\notas\application\legacy\Resumen;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Tablas de trabajo del informe STGR: se montan con nombres propios del test (`tst_resumen_*`) y se
 * borran siempre al terminar. Los datos de origen son los reales (solo lectura).
 */
final class ResumenTempTablesServiceIntegracionTest extends myTest
{
    private ResumenTempTablesService $service;
    private \PDO $pdo;
    private string $sufijo;

    /** @var list<string> */
    private array $tablas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new ResumenTempTablesService();
        $this->pdo = GlobalPdo::get('oDB');
        $this->sufijo = 'tst_resumen_' . getmypid();
    }

    public function tearDown(): void
    {
        foreach ($this->tablas as $t) {
            $this->pdo->query("DROP TABLE IF EXISTS $t CASCADE");
        }
        parent::tearDown();
    }

    private function tabla(string $nombre): string
    {
        $t = $this->sufijo . '_' . $nombre;
        $this->tablas[] = $t;

        return $t;
    }

    private function contar(string $tabla): int
    {
        return (int) $this->pdo->query("SELECT count(*) FROM $tabla")->fetchColumn();
    }

    private function notasExpediente(): string
    {
        return ConfigGlobal::mi_sfsv() == 2 ? 'publicf.e_notas' : 'publicv.e_notas';
    }

    public function test_la_tabla_principal_se_vacia_y_se_rellena_en_cada_llamada(): void
    {
        $t = $this->tabla('main');
        $fin = (date('Y') - 1) . '-09-01';
        $curso = "BETWEEN '" . (date('Y') - 2) . "-09-01' AND '$fin'";

        $this->service->rebuildMainTable($t, 'p_numerarios', $fin, $curso, false);
        $primera = $this->contar($t);
        $this->service->rebuildMainTable($t, 'p_numerarios', $fin, $curso, false);

        $this->assertSame($primera, $this->contar($t), 'TRUNCATE antes de rellenar: no se acumulan filas');
        $this->assertSame(0, (int) $this->pdo->query("SELECT count(*) FROM $t WHERE situacion NOT IN ('A','D') AND f_situacion <= '$fin'")->fetchColumn());
        $this->assertSame(0, (int) $this->pdo->query("SELECT count(*) FROM $t WHERE f_o IS NOT NULL OR f_fl IS NOT NULL OR f_orden IS NOT NULL")->fetchColumn());
    }

    public function test_en_rstgr_el_centro_lleva_la_dl_y_se_puede_filtrar_por_ella(): void
    {
        $t = $this->tabla('rstgr');
        $fin = date('Y') . '-09-01';
        $curso = "BETWEEN '" . (date('Y') - 1) . "-09-01' AND '$fin'";

        $this->service->rebuildMainTable($t, 'p_numerarios', $fin, $curso, true, ['dl-que-no-existe']);

        $this->assertSame(0, $this->contar($t), 'una dl inexistente no deja personas');

        $this->service->rebuildMainTable($t, 'p_numerarios', $fin, $curso, true);
        $conCentro = $this->pdo->query("SELECT ctr FROM $t WHERE ctr IS NOT NULL LIMIT 1")->fetchColumn();
        if ($conCentro !== false) {
            $this->assertMatchesRegularExpression('/ \(.+\)$/', (string) $conCentro);
        }
    }

    public function test_las_notas_del_curso_solo_incluyen_superadas_de_las_personas_de_la_tabla(): void
    {
        $main = $this->tabla('main2');
        $notas = $this->tabla('notas');
        $fin = date('Y') . '-09-01';
        $curso = "BETWEEN '" . (date('Y') - 40) . "-09-01' AND '$fin'";
        $this->service->rebuildMainTable($main, 'p_numerarios', $fin, $curso, false);

        $this->service->rebuildNotasTable($notas, $main, $curso, $this->notasExpediente(), [1, 3, 4]);

        $this->assertSame(0, (int) $this->pdo->query("SELECT count(*) FROM $notas WHERE id_nom NOT IN (SELECT id_nom FROM $main)")->fetchColumn());
        $this->service->rebuildNotasTable($notas, $main, $curso, $this->notasExpediente(), [1, 3, 4]);
        $dosVeces = $this->contar($notas);
        $this->service->rebuildNotasTable($notas, $main, $curso, $this->notasExpediente(), [999]);
        $this->assertSame(0, $this->contar($notas), 'sin ninguna situación superada no queda nada');
        $this->assertGreaterThanOrEqual(0, $dosVeces);
    }

    public function test_los_niveles_se_actualizan_con_las_marcas_de_fin_de_bienio_y_cuadrienio(): void
    {
        $main = $this->tabla('main3');
        $fin = date('Y') . '-09-01';
        $curso = "BETWEEN '" . (date('Y') - 40) . "-09-01' AND '$fin'";
        $this->service->rebuildMainTable($main, 'p_numerarios', $fin, $curso, false);

        $this->service->applyNivelUpdates($main, $this->notasExpediente(), $curso, 7, 8);

        $conMarca = $this->pdo->query(
            "SELECT count(DISTINCT n.id_nom) FROM {$this->notasExpediente()} n JOIN $main m USING (id_nom)
             WHERE n.id_asignatura IN (9998, 9999) AND n.f_acta $curso"
        )->fetchColumn();
        $actualizadas = $this->pdo->query("SELECT count(*) FROM $main WHERE nivel_stgr IN (7, 8)")->fetchColumn();
        $this->assertSame((int) $conMarca, (int) $actualizadas);
    }

    public function test_la_tabla_de_asignaturas_se_recrea_con_las_que_se_le_pasan(): void
    {
        $t = $this->tabla('asig');
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_asignatura')->willReturn(1101);
        $a->method('getId_nivel')->willReturn(1101);
        $a->method('getNombre_asignatura')->willReturn('Filosofía');
        $a->method('getNombre_corto')->willReturn('Fil');
        $a->method('getCreditos')->willReturn(4.5);
        $a->method('getYear')->willReturn('1');
        $a->method('getId_sector')->willReturn(2);
        $a->method('isActive')->willReturn(true);
        $a->method('getId_tipo')->willReturn(1);

        $this->service->rebuildAsignaturasTable($t, [$a, $a]);
        $this->service->rebuildAsignaturasTable($t, [$a]);

        $fila = $this->pdo->query("SELECT * FROM $t")->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(1, $fila, 'DROP + CREATE: no se acumula');
        $this->assertSame('Fil', $fila[0]['nombre_corto']);
        $this->assertSame('4.50', $fila[0]['creditos']);
    }

    public function test_la_tabla_de_profesores_trae_solo_activos_con_centro(): void
    {
        $t = $this->tabla('prof');

        $this->service->rebuildProfesorTable($t, 'p_numerarios');

        $this->assertSame(0, (int) $this->pdo->query("SELECT count(*) FROM $t WHERE ctr IS NULL")->fetchColumn());
        $this->assertSame(0, (int) $this->pdo->query("SELECT count(*) FROM $t t LEFT JOIN p_numerarios p USING (id_nom) WHERE p.situacion IS DISTINCT FROM 'A'")->fetchColumn());
    }

    public function test_query_lanza_excepcion_si_falla_y_prepare_devuelve_sentencia(): void
    {
        $this->assertSame(1, (int) $this->service->query('SELECT 1')->fetchColumn());
        $this->assertSame('x', $this->service->prepare('SELECT :v::text')->execute(['v' => 'x']) ? 'x' : '');

        $this->expectException(\Throwable::class);
        $this->service->query('SELECT * FROM tabla_que_no_existe_tst');
    }

    public function test_la_factoria_crea_un_resumen_nuevo_cada_vez(): void
    {
        $factory = $GLOBALS['container']->get(ResumenFactory::class);

        $a = $factory->create('numerarios');
        $b = $factory->create('numerarios');

        $this->assertInstanceOf(Resumen::class, $a);
        $this->assertNotSame($a, $b);
    }
}

<?php

namespace Tests\integration\misas\infrastructure\repositories;

use PDO;
use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\domain\criteria\PlantillaField;
use src\misas\domain\entity\Plantilla;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\infrastructure\GlobalPdo;
use Tests\factories\misas\PlantillaFactory;
use Tests\myTest;

/**
 * `misa_plantillas_dl` no la crea ningún DBEsquema ni migración del repo y no existe en la copia de
 * producción que se usa como BD de pruebas. Si falta, el test la crea (columnas deducidas del SQL
 * del repositorio) y la borra al terminar la clase; si existe, usa la real.
 */
class PgPlantillaRepositoryTest extends myTest
{
    private static bool $tablaCreadaPorElTest = false;

    private PlantillaRepositoryInterface $repository;
    private PlantillaFactory $factory;

    /** @var list<Plantilla> */
    private array $creadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $pdo = GlobalPdo::get('oDBE');
        if ($pdo->query("SELECT to_regclass('misa_plantillas_dl')")->fetchColumn() === null) {
            $pdo->exec('CREATE TABLE misa_plantillas_dl (
                id_item integer PRIMARY KEY, id_ctr integer NOT NULL, tarea integer NOT NULL,
                dia text NOT NULL DEFAULT \'\', semana smallint, t_start time, t_end time,
                id_nom integer, observ text)');
            $pdo->exec('CREATE SEQUENCE IF NOT EXISTS misa_plantillas_dl_id_item_seq');
            self::$tablaCreadaPorElTest = true;
        }
        $this->repository = $GLOBALS['container']->get(PlantillaRepositoryInterface::class);
        $this->factory = new PlantillaFactory();
        $this->creadas = [];
    }

    public function tearDown(): void
    {
        foreach ($this->creadas as $o) {
            $this->repository->Eliminar($o);
        }
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$tablaCreadaPorElTest) {
            $pdo = $GLOBALS['oDBE'] ?? null;
            if ($pdo instanceof PDO) {
                $pdo->exec('DROP TABLE IF EXISTS misa_plantillas_dl');
                $pdo->exec('DROP SEQUENCE IF EXISTS misa_plantillas_dl_id_item_seq');
            }
            self::$tablaCreadaPorElTest = false;
        }
        parent::tearDownAfterClass();
    }

    private function guardar(Plantilla $o): Plantilla
    {
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());
        $this->creadas[] = $o;

        return $o;
    }

    public function test_guardar_nuevo_y_leer_todos_los_campos(): void
    {
        $o = $this->factory->createSimple();
        $o->setSemana(2);
        $o->setId_nom(10011);
        $o->setObserv('turno de tarde');
        $this->guardar($o);

        $leido = $this->repository->findById($o->getId_item());

        $this->assertNotNull($leido);
        $this->assertSame($o->getId_item(), $leido->getId_item());
        $this->assertSame(1, $leido->getId_ctr());
        $this->assertSame(1, $leido->getTarea());
        $this->assertSame('test_dia', $leido->getDia());
        $this->assertSame(2, $leido->getSemana());
        $this->assertInstanceOf(TimeLocal::class, $leido->getT_start());
        $this->assertSame('12:00:00', $leido->getT_start()->format('H:i:s'));
        $this->assertSame('15:30:00', $leido->getT_end()->format('H:i:s'));
        $this->assertSame(10011, $leido->getId_nom());
        $this->assertSame('turno de tarde', $leido->getObserv());
    }

    public function test_guardar_con_campos_opcionales_a_null(): void
    {
        $o = $this->factory->createSimple();
        $o->setT_start(null);
        $o->setT_end(null);
        $this->guardar($o);

        $leido = $this->repository->findById($o->getId_item());

        $this->assertNotNull($leido);
        $this->assertNull($leido->getT_start());
        $this->assertNull($leido->getT_end());
        $this->assertNull($leido->getSemana());
        $this->assertNull($leido->getId_nom());
        $this->assertNull($leido->getObserv());
    }

    public function test_actualizar_existente_no_duplica_la_fila(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $o->setDia('m');
        $o->setTarea(4);
        $o->setT_start(TimeLocal::fromString('07:05:00'));
        $o->setObserv('cambiada');
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $leido = $this->repository->findById($o->getId_item());
        $this->assertSame('m', $leido->getDia());
        $this->assertSame(4, $leido->getTarea());
        $this->assertSame('07:05:00', $leido->getT_start()->format('H:i:s'));
        $this->assertSame('cambiada', $leido->getObserv());
        $this->assertCount(
            1,
            $this->repository->matching(Criteria::create()->where(Filter::eq(PlantillaField::IdItem, $o->getId_item())))
        );
    }

    public function test_anular_las_horas_como_hace_quitar_horario(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $o->setT_start(null);
        $o->setT_end(null);
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $leido = $this->repository->findById($o->getId_item());
        $this->assertNull($leido->getT_start());
        $this->assertNull($leido->getT_end());
        $this->assertSame('test_dia', $leido->getDia(), 'el resto de campos no cambia');
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById(2147000000));
    }

    public function test_datos_by_id(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $datos = $this->repository->datosById($o->getId_item());

        $this->assertIsArray($datos);
        $this->assertSame($o->getId_item(), $datos['id_item']);
        $this->assertInstanceOf(TimeLocal::class, $datos['t_start']);
        $this->assertFalse($this->repository->datosById(2147000001));
    }

    public function test_eliminar(): void
    {
        $o = $this->factory->createSimple();
        $this->assertTrue($this->repository->Guardar($o));
        $this->assertNotNull($this->repository->findById($o->getId_item()));

        $this->assertTrue($this->repository->Eliminar($o));
        $this->assertNull($this->repository->findById($o->getId_item()));
    }

    public function test_eliminar_inexistente_no_falla(): void
    {
        $this->assertTrue($this->repository->Eliminar($this->factory->createSimple(2147000002)));
    }

    public function test_get_new_id_es_creciente(): void
    {
        $a = $this->repository->getNewId();
        $b = $this->repository->getNewId();

        $this->assertGreaterThan(0, $a);
        $this->assertGreaterThan($a, $b);
    }

    public function test_matching_por_centro_y_orden(): void
    {
        $tarde = $this->factory->createSimple();
        $tarde->setId_ctr(990001);
        $tarde->setT_start(TimeLocal::fromString('18:00:00'));
        $manana = $this->factory->createSimple();
        $manana->setId_ctr(990001);
        $manana->setT_start(TimeLocal::fromString('07:00:00'));
        $otroCtr = $this->factory->createSimple();
        $otroCtr->setId_ctr(990002);
        foreach ([$tarde, $manana, $otroCtr] as $o) {
            $this->guardar($o);
        }

        $res = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(PlantillaField::IdCtr, 990001))
                ->orderBy(PlantillaField::TStart)
        );

        $this->assertSame(
            [$manana->getId_item(), $tarde->getId_item()],
            array_map(static fn(Plantilla $p) => $p->getId_item(), $res)
        );
        $this->assertSame('07:00:00', $res[0]->getT_start()->format('H:i:s'));
    }

    public function test_matching_sin_resultados(): void
    {
        $this->assertSame([], $this->repository->matching(
            Criteria::create()->where(Filter::eq(PlantillaField::IdItem, 2147000003))
        ));
    }
}

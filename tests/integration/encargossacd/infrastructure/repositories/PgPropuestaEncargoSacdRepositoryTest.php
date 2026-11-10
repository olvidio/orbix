<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdField;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\encargossacd\PropuestaEncargoSacdFactory;
use Tests\myTest;

/**
 * La tabla staging `propuesta_encargos_sacd` solo existe cuando se ha creado la propuesta
 * (DBPropuestas): si falta en la base de pruebas, se omiten los tests.
 */
class PgPropuestaEncargoSacdRepositoryTest extends myTest
{
    private PropuestaEncargoSacdRepositoryInterface $repository;
    private PropuestaEncargoSacdFactory $factory;

    /** @var list<PropuestaEncargoSacd> */
    private array $creadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PropuestaEncargoSacdRepositoryInterface::class);
        if (!$this->repository->existenLasTablas()) {
            $this->markTestSkipped('No existe propuesta_encargos_sacd en la base de pruebas.');
        }
        $this->factory = new PropuestaEncargoSacdFactory();
        $this->creadas = [];
    }

    public function tearDown(): void
    {
        foreach ($this->creadas as $o) {
            $this->repository->Eliminar($o);
        }
        parent::tearDown();
    }

    private function guardar(PropuestaEncargoSacd $o): PropuestaEncargoSacd
    {
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());
        $this->creadas[] = $o;

        return $o;
    }

    public function test_existen_las_tablas(): void
    {
        $this->assertTrue($this->repository->existenLasTablas());
    }

    public function test_guardar_nuevo_y_leer_todos_los_campos(): void
    {
        $o = $this->factory->createSimple(null, 10099);
        $o->setF_fin(new DateTimeLocal('2024-06-30'));
        $this->guardar($o);

        $leido = $this->repository->findById($o->getId_item());

        $this->assertNotNull($leido);
        $this->assertSame($o->getId_item(), $leido->getId_item());
        $this->assertSame(3, $leido->getId_enc());
        $this->assertSame(10011, $leido->getId_nom());
        $this->assertInstanceOf(EncargoModoId::class, $leido->getModoVo());
        $this->assertSame(3, $leido->getModoVo()->value());
        $this->assertSame('2023-04-05', $leido->getF_ini()->format('Y-m-d'));
        $this->assertSame('2024-06-30', $leido->getF_fin()?->format('Y-m-d'));
        $this->assertSame(10099, $leido->getId_nom_new());
    }

    public function test_guardar_con_campos_opcionales_a_null(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $leido = $this->repository->findById($o->getId_item());

        $this->assertNull($leido->getF_fin());
        $this->assertNull($leido->getId_nom_new());
    }

    public function test_guardar_con_id_cero_asigna_id_de_la_secuencia(): void
    {
        $o = $this->factory->createSimple(0);

        $this->guardar($o);

        $this->assertGreaterThan(0, $o->getId_item());
        $this->assertNotNull($this->repository->findById($o->getId_item()));
    }

    public function test_actualizar_existente_no_duplica_la_fila(): void
    {
        $o = $this->guardar($this->factory->createSimple());

        $o->setModoVo(new EncargoModoId(2));
        $o->setId_nom(10012);
        $o->setId_nom_new(10077);
        $o->setF_fin(new DateTimeLocal('2025-01-31'));
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $leido = $this->repository->findById($o->getId_item());
        $this->assertSame(2, $leido->getModoVo()->value());
        $this->assertSame(10012, $leido->getId_nom());
        $this->assertSame(10077, $leido->getId_nom_new());
        $this->assertSame('2025-01-31', $leido->getF_fin()?->format('Y-m-d'));
        $this->assertCount(
            1,
            $this->repository->matching(Criteria::create()->where(Filter::eq(PropuestaEncargoSacdField::IdItem, $o->getId_item())))
        );
    }

    public function test_actualizar_f_fin_a_null(): void
    {
        $o = $this->factory->createSimple();
        $o->setF_fin(new DateTimeLocal('2024-06-30'));
        $this->guardar($o);

        $o->setF_fin(null);
        $this->assertTrue($this->repository->Guardar($o), $this->repository->getErrorTxt());

        $this->assertNull($this->repository->findById($o->getId_item())->getF_fin());
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById(2147000000));
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
        $this->assertTrue($this->repository->Eliminar($this->factory->createSimple(2147000001)));
    }

    public function test_get_new_id_es_creciente(): void
    {
        $a = $this->repository->getNewId();
        $b = $this->repository->getNewId();

        $this->assertGreaterThan(0, $a);
        $this->assertGreaterThan($a, $b);
    }

    public function test_clave_unica_enc_nom_new_modo_f_ini_lanza_excepcion(): void
    {
        $this->guardar($this->factory->createSimple(null, 10099));

        $duplicada = $this->factory->createSimple(null, 10099);
        try {
            $this->repository->Guardar($duplicada);
            $this->fail('(id_enc, id_nom_new, modo, f_ini) es único: debía lanzar RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('propuesta_encargos_sacd_id_enc_ukey', $e->getMessage());
        }
        $this->assertNull($this->repository->findById($duplicada->getId_item()));
    }

    public function test_con_id_nom_new_null_no_aplica_la_clave_unica(): void
    {
        $this->guardar($this->factory->createSimple());
        $this->guardar($this->factory->createSimple());

        $this->addToAssertionCount(1);
    }

    public function test_matching_por_id_nom_new_f_fin_null_y_orden(): void
    {
        $a = $this->factory->createSimple(null, 10098);
        $a->setModoVo(new EncargoModoId(3));
        $b = $this->factory->createSimple(null, 10098);
        $b->setModoVo(new EncargoModoId(2));
        $c = $this->factory->createSimple(null, 10098);
        $c->setModoVo(new EncargoModoId(1));
        $c->setF_fin(new DateTimeLocal('2024-06-30'));
        foreach ([$a, $b, $c] as $o) {
            $this->guardar($o);
        }

        $res = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(PropuestaEncargoSacdField::IdNomNew, 10098))
                ->andWhere(Filter::isNull(PropuestaEncargoSacdField::FFin))
                ->orderBy(PropuestaEncargoSacdField::Modo)
        );

        $this->assertSame(
            [$b->getId_item(), $a->getId_item()],
            array_map(static fn(PropuestaEncargoSacd $p) => $p->getId_item(), $res),
            'solo las vigentes (f_fin nula), ordenadas por modo: 2 antes que 3'
        );
        $this->assertContainsOnlyInstancesOf(PropuestaEncargoSacd::class, $res);
    }

    public function test_matching_sin_resultados(): void
    {
        $this->assertSame([], $this->repository->matching(
            Criteria::create()->where(Filter::eq(PropuestaEncargoSacdField::IdItem, 2147000002))
        ));
    }
}

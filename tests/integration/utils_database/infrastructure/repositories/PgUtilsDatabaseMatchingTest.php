<?php

namespace Tests\integration\utils_database\infrastructure\repositories;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\criteria\DbSchemaField;
use src\utils_database\domain\criteria\MapIdField;
use Tests\factories\utils_database\DbSchemaFactory;
use Tests\factories\utils_database\MapIdFactory;
use Tests\myTest;

/**
 * `matching()` de PgDbSchemaRepository y PgMapIdRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUtilsDatabaseMatchingTest extends myTest
{
    private DbSchemaRepositoryInterface $schemaRepo;
    private MapIdRepositoryInterface $mapIdRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->schemaRepo = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
        $this->mapIdRepo = $GLOBALS['container']->get(MapIdRepositoryInterface::class);
    }

    public function test_db_schema_matching_por_schema_equivale_a_legacy(): void
    {
        $factory = new DbSchemaFactory();
        $schema = 'tst_match_' . random_int(1000, 9999);
        $oDbSchema = $factory->createSimple($schema);
        $this->schemaRepo->Guardar($oDbSchema);

        $nuevo = $this->schemaRepo->matching(Criteria::create()->where(Filter::eq(DbSchemaField::Schema, $schema)));
        $legacy = $this->schemaRepo->getDbSchemas(['schema' => $schema]);

        $this->assertCount(1, $nuevo);
        $this->assertSame($schema, $nuevo[0]->getSchema());
        $this->assertEquals($legacy, $nuevo);

        $this->schemaRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->schemaRepo->matching(Criteria::create()->where(Filter::eq(DbSchemaField::Schema, $schema))));
    }

    public function test_db_schema_matching_orden_y_limite(): void
    {
        $factory = new DbSchemaFactory();
        $sufijo = random_int(1000, 9999);
        $a = $factory->createSimple('tst_ord_a_' . $sufijo);
        $this->schemaRepo->Guardar($a);
        $b = $factory->createSimple('tst_ord_b_' . $sufijo);
        $this->schemaRepo->Guardar($b);

        $res = $this->schemaRepo->matching(
            Criteria::create()
                ->where(Filter::in(DbSchemaField::Schema, [$a->getSchema(), $b->getSchema()]))
                ->orderBy(DbSchemaField::Schema, \src\shared\domain\criteria\OrderDirection::Desc)
                ->limit(1)
        );

        $this->assertCount(1, $res);
        $this->assertSame($b->getSchema(), $res[0]->getSchema());

        $this->schemaRepo->Eliminar($a);
        $this->schemaRepo->Eliminar($b);
    }

    public function test_map_id_matching_por_clave_equivale_a_legacy(): void
    {
        $factory = new MapIdFactory();
        $idResto = random_int(90000, 99999);
        $oMapId = $factory->createSimple('tstm', $idResto);
        $this->mapIdRepo->Guardar($oMapId);

        $criteria = Criteria::create()
            ->where(Filter::eq(MapIdField::Objeto, 'tstm'))
            ->andWhere(Filter::eq(MapIdField::IdResto, $idResto));
        $nuevo = $this->mapIdRepo->matching($criteria);
        $legacy = $this->mapIdRepo->getMapIdes(['objeto' => 'tstm', 'id_resto' => $idResto]);

        $this->assertCount(1, $nuevo);
        $this->assertSame($idResto, $nuevo[0]->getId_resto());
        $this->assertEquals($legacy, $nuevo);

        $this->mapIdRepo->Eliminar($nuevo[0]);
        $this->assertSame([], $this->mapIdRepo->matching($criteria));
    }
}

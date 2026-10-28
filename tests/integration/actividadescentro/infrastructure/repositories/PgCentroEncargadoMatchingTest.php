<?php

namespace Tests\integration\actividadescentro\infrastructure\repositories;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\criteria\CentroEncargadoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\factories\actividadescentro\CentroEncargadoFactory;
use Tests\myTest;

/**
 * `matching()` de PgCentroEncargadoRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgCentroEncargadoMatchingTest extends myTest
{
    private CentroEncargadoRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
    }

    public function test_matching_por_actividad_y_centro_equivale_a_legacy(): void
    {
        $oCentro = (new CentroEncargadoFactory())->createSimple();
        $this->repository->Guardar($oCentro, false);

        $criteria = Criteria::create()
            ->where(Filter::eq(CentroEncargadoField::IdActiv, $oCentro->getId_activ()))
            ->andWhere(Filter::eq(CentroEncargadoField::IdUbi, $oCentro->getId_ubi()))
            ->orderBy(CentroEncargadoField::NumOrden, OrderDirection::Desc);
        $nuevo = $this->repository->matching($criteria);
        $legacy = $this->repository->getCentrosEncargados(
            ['id_activ' => $oCentro->getId_activ(), 'id_ubi' => $oCentro->getId_ubi(), '_ordre' => 'num_orden DESC']
        );

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $this->repository->Eliminar($nuevo[0], false);
        $this->assertSame([], $this->repository->matching($criteria));
    }

    public function test_matching_sin_filtros_devuelve_lista(): void
    {
        $this->assertIsArray($this->repository->matching(Criteria::create()->limit(3)));
    }
}

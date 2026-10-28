<?php

namespace Tests\integration\tablonanuncios\infrastructure\repositories;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\criteria\AnuncioField;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use Tests\factories\tablonanuncios\AnuncioFactory;
use Tests\myTest;

/**
 * `matching()` de PgAnuncioRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgAnuncioMatchingTest extends myTest
{
    private AnuncioRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AnuncioRepositoryInterface::class);
    }

    public function test_matching_por_id_tablon_y_no_eliminado_equivale_a_legacy(): void
    {
        $anuncio = (new AnuncioFactory())->createSimple();
        $id = $anuncio->getUuid_item();
        $this->repository->Guardar($anuncio);

        $criteria = Criteria::create()
            ->where(Filter::eq(AnuncioField::UuidItem, $id->value()))
            ->andWhere(Filter::matchesInsensitive(AnuncioField::Tablon, 'EST'))
            ->andWhere(Filter::isNull(AnuncioField::TEliminado));
        $nuevo = $this->repository->matching($criteria);
        $legacy = $this->repository->getAnuncios(
            ['uuid_item' => $id->value(), 'tablon' => 'EST', 't_eliminado' => 'x'],
            ['tablon' => '~*', 't_eliminado' => 'IS NULL']
        );

        $this->assertCount(1, $nuevo);
        $this->assertEquals($id, $nuevo[0]->getUuid_item());
        $this->assertNotNull($nuevo[0]->getT_anotado());
        $this->assertEquals($legacy, $nuevo);

        $this->repository->Eliminar($nuevo[0]);
    }

    public function test_matching_sin_coincidencias_devuelve_vacio(): void
    {
        $res = $this->repository->matching(
            Criteria::create()->where(Filter::eq(AnuncioField::UuidItem, AnuncioId::random()->value()))
        );

        $this->assertSame([], $res);
    }
}

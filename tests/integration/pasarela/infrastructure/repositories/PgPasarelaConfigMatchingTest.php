<?php

namespace Tests\integration\pasarela\infrastructure\repositories;

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\criteria\PasarelaConfigField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\factories\pasarela\PasarelaConfigFactory;
use Tests\myTest;

/**
 * `matching()` de PgPasarelaConfigRepository (paso 4.1 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgPasarelaConfigMatchingTest extends myTest
{
    private PasarelaConfigRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PasarelaConfigRepositoryInterface::class);
    }

    public function test_matching_por_nombre_y_decodifica_el_json(): void
    {
        $oConfig = (new PasarelaConfigFactory())->createSimple();
        $nombre = $oConfig->getNom_parametro();
        $this->repository->Guardar($oConfig);

        $criteria = Criteria::create()->where(Filter::eq(PasarelaConfigField::NomParametro, $nombre));
        $nuevo = $this->repository->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame(['valor' => 'test'], $nuevo[0]->getJson_valor(true));

        $this->repository->Eliminar($nuevo[0]);
        $this->assertSame([], $this->repository->matching($criteria));
    }
}

<?php

namespace Tests\integration\shared\infrastructure\repositories;

use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\criteria\ColaMailField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\shared\ColaMailFactory;
use Tests\myTest;

/**
 * `matching()` de PgColaMailRepository (paso 4.2 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgColaMailMatchingTest extends myTest
{
    private ColaMailRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ColaMailRepositoryInterface::class);
    }

    public function test_matching_pendientes_equivale_a_legacy(): void
    {
        $factory = new ColaMailFactory();
        $pendiente = $factory->createSimple();
        $enviado = $factory->createSimple();
        $enviado->setSended(new DateTimeLocal());
        $this->repository->Guardar($pendiente);
        $this->repository->Guardar($enviado);

        $idPendiente = $pendiente->getUuid_item()->value();
        $idEnviado = $enviado->getUuid_item()->value();
        try {
            $criteria = Criteria::create()
                ->where(Filter::isNull(ColaMailField::Sended))
                ->andWhere(Filter::in(ColaMailField::UuidItem, [$idPendiente, $idEnviado]));
            $nuevo = $this->repository->matching($criteria);

            $this->assertCount(1, $nuevo);
            $this->assertSame($idPendiente, $nuevo[0]->getUuid_item()->value());

            $limitado = $this->repository->matching(
                Criteria::create()->where(Filter::isNull(ColaMailField::Sended))->limit(1)
            );
            $this->assertLessThanOrEqual(1, count($limitado));
        } finally {
            $this->repository->Eliminar($pendiente);
            $this->repository->Eliminar($enviado);
        }
    }
}

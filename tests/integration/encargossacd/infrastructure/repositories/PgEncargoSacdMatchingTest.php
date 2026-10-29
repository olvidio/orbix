<?php

namespace Tests\integration\encargossacd\infrastructure\persistence\postgresql;

use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoSacdField;
use src\encargossacd\domain\criteria\EncargoSacdObservField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use Tests\factories\encargossacd\EncargoSacdFactory;
use Tests\myTest;

/**
 * `matching()` de `EncargoSacd` y `EncargoSacdObserv` (paso 4.5 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgEncargoSacdMatchingTest extends myTest
{
    public function test_encargo_sacd_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $oEncargoSacd = (new EncargoSacdFactory())->createSimple();
        $this->assertTrue($repo->Guardar($oEncargoSacd));

        try {
            $id_enc = $oEncargoSacd->getId_enc();
            $id_nom = $oEncargoSacd->getId_nom();

            $nuevo = $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoSacdField::IdEnc, $id_enc))
                    ->andWhere(Filter::eq(EncargoSacdField::IdNom, $id_nom))
                    ->orderBy(EncargoSacdField::Modo)
                    ->addOrderBy(EncargoSacdField::FIni, OrderDirection::Desc)
            );
            $this->assertNotEmpty($nuevo);
            $this->assertEquals(
                $repo->getEncargosSacd(['id_enc' => $id_enc, 'id_nom' => $id_nom, '_ordre' => 'modo, f_ini DESC']),
                $nuevo
            );

            $this->assertEquals(
                $repo->getEncargosSacd(['id_nom' => $id_nom, 'f_fin' => 'x', '_ordre' => 'modo'], ['f_fin' => 'IS NULL']),
                $repo->matching(
                    Criteria::create()
                        ->where(Filter::eq(EncargoSacdField::IdNom, $id_nom))
                        ->andWhere(Filter::isNull(EncargoSacdField::FFin))
                        ->orderBy(EncargoSacdField::Modo)
                )
            );
            $this->assertEquals(
                $repo->getEncargosSacd(['id_nom' => $id_nom, 'modo' => '(2|3|5)', '_ordre' => 'id_item'], ['modo' => '~']),
                $repo->matching(
                    Criteria::create()
                        ->where(Filter::eq(EncargoSacdField::IdNom, $id_nom))
                        ->andWhere(Filter::matches(EncargoSacdField::Modo, '(2|3|5)'))
                        ->orderBy(EncargoSacdField::IdItem)
                )
            );
        } finally {
            $repo->Eliminar($oEncargoSacd);
        }
    }

    public function test_encargo_sacd_observ_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoSacdObservRepositoryInterface::class);

        $this->assertEquals(
            $repo->getEncargosSacdObservs(['id_nom' => 1, '_ordre' => 'id_item']),
            $repo->matching(
                Criteria::create()->where(Filter::eq(EncargoSacdObservField::IdNom, 1))->orderBy(EncargoSacdObservField::IdItem)
            )
        );
    }
}

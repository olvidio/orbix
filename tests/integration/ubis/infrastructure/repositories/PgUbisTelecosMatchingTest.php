<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\criteria\TelecoUbiField;
use Tests\factories\ubis\TelecoUbiFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de telecos de `ubis` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbisTelecosMatchingTest extends myTest
{
    public function test_teleco_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
        $o = (new TelecoUbiFactory())->createSimple();
        $idUbi = $o->getId_ubi();
        $repo->Guardar($o);

        $criteria = Criteria::create()
            ->where(Filter::eq(TelecoUbiField::IdUbi, $idUbi))
            ->andWhere(Filter::eq(TelecoUbiField::IdTipoTeleco, 3))
            ->orderBy(TelecoUbiField::IdItem);
        $nuevo = $repo->matching($criteria);

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($idUbi, $nuevo[0]->getId_ubi());
        $this->assertEquals(3, $nuevo[0]->getId_tipo_teleco());

        $repo->Eliminar($o);
    }

    public function test_teleco_matching_sin_acentos_y_variante_cdc(): void
    {
        $ctr = $GLOBALS['container']->get(TelecoCtrDlRepositoryInterface::class);
        $o = (new TelecoUbiFactory())->createSimple();
        $ctr->Guardar($o);

        $nuevo = $ctr->matching(
            Criteria::create()->where(Filter::matchesUnaccented(TelecoUbiField::NumTeleco, 'test_num_teleco_vo'))
        );
        $this->assertNotEmpty($nuevo);
        $this->assertEquals('test_num_teleco_vo', $nuevo[0]->getNum_teleco());
        $ctr->Eliminar($o);

        $cdc = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
        $criteria = Criteria::create()->orderBy(TelecoUbiField::IdItem)->limit(5);
        $this->assertLessThanOrEqual(5, count($cdc->matching($criteria)));
    }
}

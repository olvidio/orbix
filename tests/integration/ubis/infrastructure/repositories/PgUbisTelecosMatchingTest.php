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
        $legacy = $repo->getTelecos(['id_ubi' => $idUbi, 'id_tipo_teleco' => 3, '_ordre' => 'id_item']);

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);

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
        $legacy = $ctr->getTelecos(['num_teleco' => 'test_num_teleco_vo'], ['num_teleco' => 'sin_acentos']);
        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);
        $ctr->Eliminar($o);

        $cdc = $GLOBALS['container']->get(TelecoCdcDlRepositoryInterface::class);
        $criteria = Criteria::create()->orderBy(TelecoUbiField::IdItem)->limit(5);
        $this->assertEquals($cdc->getTelecos(['_ordre' => 'id_item', '_limit' => 5]), $cdc->matching($criteria));
    }
}

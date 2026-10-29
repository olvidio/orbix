<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\criteria\CasaField;
use Tests\myTest;

/**
 * `matching()` de los repositorios de casas de `ubis` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbisCasasMatchingTest extends myTest
{
    public function test_casa_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CasaRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(CasaField::Active, true))
                ->andWhere(Filter::eq(CasaField::Sv, true))
                ->orderBy(CasaField::NombreUbi)
        );
        $legacy = $repo->getCasas(['active' => 't', 'sv' => 't', '_ordre' => 'nombre_ubi']);

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($legacy, $nuevo);
    }

    public function test_casa_matching_regex_y_negado_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CasaRepositoryInterface::class);

        $this->assertEquals(
            $repo->getCasas(['tipo_casa' => 'cdc|cdr', '_ordre' => 'nombre_ubi'], ['tipo_casa' => '~']),
            $repo->matching(
                Criteria::create()->where(Filter::matches(CasaField::TipoCasa, 'cdc|cdr'))->orderBy(CasaField::NombreUbi)
            )
        );
        $this->assertEquals(
            $repo->getCasas(['tipo_casa' => 'cdc|cdr|cgi', '_ordre' => 'nombre_ubi'], ['tipo_casa' => '!~']),
            $repo->matching(
                Criteria::create()->where(Filter::notMatches(CasaField::TipoCasa, 'cdc|cdr|cgi'))->orderBy(CasaField::NombreUbi)
            )
        );
    }

    public function test_variantes_dl_y_ex_heredan_matching(): void
    {
        $dl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $ex = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        $criteria = Criteria::create()->where(Filter::matchesUnaccented(CasaField::NombreUbi, 'a'))->orderBy(CasaField::IdUbi);

        $this->assertEquals(
            $dl->getCasas(['nombre_ubi' => 'a', '_ordre' => 'id_ubi'], ['nombre_ubi' => 'sin_acentos']),
            $dl->matching($criteria)
        );
        $this->assertEquals(
            $ex->getCasas(['nombre_ubi' => 'a', '_ordre' => 'id_ubi'], ['nombre_ubi' => 'sin_acentos']),
            $ex->matching($criteria)
        );
    }
}

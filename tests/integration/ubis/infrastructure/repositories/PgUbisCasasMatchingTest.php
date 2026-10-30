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
    public function test_casa_matching_filtra_y_ordena(): void
    {
        $repo = $GLOBALS['container']->get(CasaRepositoryInterface::class);

        $casas = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(CasaField::Active, true))
                ->andWhere(Filter::eq(CasaField::Sv, true))
                ->orderBy(CasaField::NombreUbi)
        );

        $this->assertNotEmpty($casas);
        foreach ($casas as $casa) {
            $this->assertTrue($casa->isActive());
            $this->assertTrue($casa->isSv());
        }
    }

    public function test_casa_matching_regex_y_negado_son_complementarios(): void
    {
        $repo = $GLOBALS['container']->get(CasaRepositoryInterface::class);

        $conTipo = $repo->matching(
            Criteria::create()->where(Filter::matches(CasaField::TipoCasa, 'cdc|cdr'))->orderBy(CasaField::NombreUbi)
        );
        foreach ($conTipo as $casa) {
            $this->assertMatchesRegularExpression('/cdc|cdr/', (string) $casa->getTipo_casa());
        }

        $sinTipo = $repo->matching(
            Criteria::create()->where(Filter::notMatches(CasaField::TipoCasa, 'cdc|cdr|cgi'))->orderBy(CasaField::NombreUbi)
        );
        foreach ($sinTipo as $casa) {
            $this->assertDoesNotMatchRegularExpression('/cdc|cdr|cgi/', (string) $casa->getTipo_casa());
        }
    }

    public function test_variantes_dl_y_ex_heredan_matching(): void
    {
        $dl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $ex = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        $criteria = Criteria::create()->where(Filter::matchesUnaccented(CasaField::NombreUbi, 'a'))->orderBy(CasaField::IdUbi);

        foreach ([$dl, $ex] as $repo) {
            $ids = array_map(static fn ($c) => $c->getId_ubi(), $repo->matching($criteria));
            $ordenados = $ids;
            sort($ordenados);
            $this->assertSame($ordenados, $ids);
        }
    }
}

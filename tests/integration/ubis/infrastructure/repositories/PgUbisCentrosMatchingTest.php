<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\CentroEllasField;
use src\ubis\domain\criteria\CentroEllosField;
use src\ubis\domain\criteria\CentroExField;
use src\ubis\domain\criteria\CentroField;
use Tests\factories\ubis\CentroDlFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de centros de `ubis` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbisCentrosMatchingTest extends myTest
{
    public function test_centro_dl_matching_booleano_y_orden(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $o = (new CentroDlFactory())->createSimple();
        $id = $o->getId_ubi();
        $repo->Guardar($o);

        $criteria = Criteria::create()
            ->where(Filter::eq(CentroDlField::IdUbi, $id))
            ->andWhere(Filter::eq(CentroDlField::Active, false))
            ->orderBy(CentroDlField::NombreUbi);
        $nuevo = $repo->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertSame($id, $nuevo[0]->getId_ubi());
        $this->assertFalse($nuevo[0]->isActive());

        $repo->Eliminar($o);
    }

    public function test_centro_dl_matching_regex_in_y_bits(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::matches(CentroDlField::TipoCtr, '^s[^s]'))
                ->andWhere(Filter::eq(CentroDlField::Active, true))
                ->orderBy(CentroDlField::NombreUbi)
        );
        foreach ($nuevo as $centro) {
            $this->assertMatchesRegularExpression('/^s[^s]/', (string) $centro->getTipo_ctr());
            $this->assertTrue($centro->isActive());
        }

        $nuevoBits = $repo->matching(
            Criteria::create()->where(Filter::hasBits(CentroDlField::TipoLabor, 512))->orderBy(CentroDlField::IdUbi)
        );
        foreach ($nuevoBits as $centro) {
            $this->assertSame(512, ((int) $centro->getTipo_labor()) & 512);
        }

        $todos = $repo->matching(Criteria::create()->orderBy(CentroDlField::IdUbi)->limit(3));
        $ids = array_map(static fn ($c) => $c->getId_ubi(), $todos);
        $nuevoIn = $repo->matching(Criteria::create()->where(Filter::in(CentroDlField::IdUbi, $ids))->orderBy(CentroDlField::IdUbi));
        $this->assertSame($ids, array_map(static fn ($c) => $c->getId_ubi(), $nuevoIn));
    }

    public function test_otros_repos_de_centros(): void
    {
        $centro = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $primeros = $centro->matching(Criteria::create()->orderBy(CentroField::IdUbi)->limit(5));
        $this->assertLessThanOrEqual(5, count($primeros));

        $ex = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        foreach ($ex->matching(
            Criteria::create()
                ->where(Filter::matches(CentroExField::TipoCtr, 'cr|dl'))
                ->andWhere(Filter::eq(CentroExField::Active, true))
                ->orderBy(CentroExField::NombreUbi)
        ) as $c) {
            $this->assertTrue($c->isActive());
        }

        $ellas = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        foreach ($ellas->matching(Criteria::create()->where(Filter::eq(CentroEllasField::Cdc, true))->orderBy(CentroEllasField::NombreUbi)) as $c) {
            $this->assertTrue($c->isCdc());
        }

        $ellos = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
        foreach ($ellos->matching(Criteria::create()->where(Filter::eq(CentroEllosField::Active, true))->orderBy(CentroEllosField::NombreUbi)) as $c) {
            $this->assertTrue($c->isActive());
        }
    }
}

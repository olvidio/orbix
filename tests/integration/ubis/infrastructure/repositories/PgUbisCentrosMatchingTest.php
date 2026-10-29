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
    public function test_centro_dl_matching_booleano_y_orden_equivale_a_legacy(): void
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
        $legacy = $repo->getCentros(['id_ubi' => $id, 'active' => 'f', '_ordre' => 'nombre_ubi']);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_centro_dl_matching_regex_in_y_bits_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::matches(CentroDlField::TipoCtr, '^s[^s]'))
                ->andWhere(Filter::eq(CentroDlField::Active, true))
                ->orderBy(CentroDlField::NombreUbi)
        );
        $legacy = $repo->getCentros(
            ['tipo_ctr' => '^s[^s]', 'active' => 't', '_ordre' => 'nombre_ubi'],
            ['tipo_ctr' => '~']
        );
        $this->assertEquals($legacy, $nuevo);

        $nuevoBits = $repo->matching(
            Criteria::create()->where(Filter::hasBits(CentroDlField::TipoLabor, 512))->orderBy(CentroDlField::IdUbi)
        );
        $legacyBits = $repo->getCentros(['tipo_labor' => '512', '_ordre' => 'id_ubi'], ['tipo_labor' => '&']);
        $this->assertEquals($legacyBits, $nuevoBits);

        $ids = array_slice(array_map(static fn ($c) => $c->getId_ubi(), $repo->getCentros(['_limit' => 3])), 0, 3);
        $nuevoIn = $repo->matching(Criteria::create()->where(Filter::in(CentroDlField::IdUbi, $ids))->orderBy(CentroDlField::IdUbi));
        $legacyIn = $repo->getCentros(['id_ubi' => implode(',', $ids), '_ordre' => 'id_ubi'], ['id_ubi' => 'IN']);
        $this->assertEquals($legacyIn, $nuevoIn);
    }

    public function test_otros_repos_de_centros_equivalen_a_legacy(): void
    {
        $centro = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $this->assertEquals(
            $centro->getCentros(['_ordre' => 'id_ubi', '_limit' => 5]),
            $centro->matching(Criteria::create()->orderBy(CentroField::IdUbi)->limit(5))
        );

        $ex = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        $this->assertEquals(
            $ex->getCentros(['tipo_ctr' => 'cr|dl', 'active' => 't', '_ordre' => 'nombre_ubi'], ['tipo_ctr' => '~']),
            $ex->matching(
                Criteria::create()
                    ->where(Filter::matches(CentroExField::TipoCtr, 'cr|dl'))
                    ->andWhere(Filter::eq(CentroExField::Active, true))
                    ->orderBy(CentroExField::NombreUbi)
            )
        );

        $ellas = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $this->assertEquals(
            $ellas->getCentros(['cdc' => 't', '_ordre' => 'nombre_ubi']),
            $ellas->matching(Criteria::create()->where(Filter::eq(CentroEllasField::Cdc, true))->orderBy(CentroEllasField::NombreUbi))
        );

        $ellos = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
        $this->assertEquals(
            $ellos->getCentros(['active' => 't', '_ordre' => 'nombre_ubi']),
            $ellos->matching(Criteria::create()->where(Filter::eq(CentroEllosField::Active, true))->orderBy(CentroEllosField::NombreUbi))
        );
    }
}

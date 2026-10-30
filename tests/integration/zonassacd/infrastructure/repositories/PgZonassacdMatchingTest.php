<?php

namespace Tests\integration\zonassacd\infrastructure\repositories;

use src\shared\domain\criteria\Criteria;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaField;
use src\zonassacd\domain\criteria\ZonaGrupoField;
use src\zonassacd\domain\criteria\ZonaSacdField;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `zonassacd` (paso 4.2 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes.
 */
class PgZonassacdMatchingTest extends myTest
{
    public function test_zonas_ordenadas_por_orden_e_id(): void
    {
        $repo = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $zonas = $repo->matching(Criteria::create()->orderBy(ZonaField::Orden)->addOrderBy(ZonaField::IdZona));
        $this->assertIsArray($zonas);
        $claves = array_map(static fn($z) => [$z->getOrden() ?? PHP_INT_MAX, $z->getId_zona()], $zonas);
        $esperado = $claves;
        usort($esperado, static fn($a, $b) => $a <=> $b);
        $this->assertSame($esperado, $claves);
    }

    public function test_zonas_grupo_y_zonas_sacd_ordenadas(): void
    {
        $grupos = $GLOBALS['container']->get(ZonaGrupoRepositoryInterface::class);
        $lista = $grupos->matching(Criteria::create()->orderBy(ZonaGrupoField::Orden)->addOrderBy(ZonaGrupoField::IdGrupo));
        $this->assertIsArray($lista);
        $claves = array_map(static fn($g) => [$g->getOrden() ?? PHP_INT_MAX, $g->getId_grupo()], $lista);
        $esperado = $claves;
        usort($esperado, static fn($a, $b) => $a <=> $b);
        $this->assertSame($esperado, $claves);

        $sacds = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $orden = $sacds->matching(Criteria::create()->orderBy(ZonaSacdField::IdItem));
        $this->assertIsArray($orden);
        $ids = array_map(static fn($z) => $z->getId_item(), $orden);
        $esperado = $ids;
        sort($esperado);
        $this->assertSame($esperado, $ids);
    }
}

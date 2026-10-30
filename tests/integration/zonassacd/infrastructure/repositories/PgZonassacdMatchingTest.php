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
    public function test_zonas_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $this->assertEquals(
            $repo->getZonas(['_ordre' => 'orden, id_zona']),
            $repo->matching(Criteria::create()->orderBy(ZonaField::Orden)->addOrderBy(ZonaField::IdZona))
        );
    }

    public function test_zonas_grupo_y_zonas_sacd_equivalen_a_legacy(): void
    {
        $grupos = $GLOBALS['container']->get(ZonaGrupoRepositoryInterface::class);
        $this->assertEquals(
            $grupos->getZonasGrupo(['_ordre' => 'orden, id_grupo']),
            $grupos->matching(Criteria::create()->orderBy(ZonaGrupoField::Orden)->addOrderBy(ZonaGrupoField::IdGrupo))
        );

        $sacds = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $orden = $sacds->matching(Criteria::create()->orderBy(ZonaSacdField::IdItem));
        $this->assertIsArray($orden);
        $ids = array_map(static fn($z) => $z->getId_item(), $orden);
        $esperado = $ids;
        sort($esperado);
        $this->assertSame($esperado, $ids);
    }
}

<?php

namespace Tests\integration\configuracion\infrastructure\repositories;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\criteria\AppField;
use src\configuracion\domain\criteria\ConfigSchemaField;
use src\configuracion\domain\criteria\ModuloField;
use src\configuracion\domain\criteria\ModuloInstaladoField;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `configuracion` (paso 4.2 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes (las tablas de módulos y apps son de catálogo).
 */
class PgConfiguracionMatchingTest extends myTest
{
    public function test_modulos_con_arrays_pg_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ModuloRepositoryInterface::class);
        $nuevo = $repo->matching(Criteria::create()->orderBy(ModuloField::Nom));

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($repo->getModulos(['_ordre' => 'nom']), $nuevo);
    }

    public function test_apps_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(AppRepositoryInterface::class);
        $nuevo = $repo->matching(Criteria::create()->orderBy(AppField::Nom));

        $this->assertNotEmpty($nuevo);
        $this->assertEquals($repo->getApps(['_ordre' => 'nom']), $nuevo);
    }

    public function test_modulos_instalados_y_config_schema_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ModuloInstaladoRepositoryInterface::class);
        $this->assertEquals(
            $repo->getModuloInstalados(['_ordre' => 'id_mod']),
            $repo->matching(Criteria::create()->orderBy(ModuloInstaladoField::IdMod))
        );

        $config = $GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class);
        $this->assertEquals(
            $config->getConfigSchemas(['_ordre' => 'parametro']),
            $config->matching(Criteria::create()->orderBy(ConfigSchemaField::Parametro))
        );
    }
}

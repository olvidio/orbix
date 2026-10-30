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
        $this->assertContainsOnlyInstancesOf(\src\configuracion\domain\entity\Modulo::class, $nuevo);
    }

    public function test_apps_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(AppRepositoryInterface::class);
        $nuevo = $repo->matching(Criteria::create()->orderBy(AppField::Nom));

        $this->assertNotEmpty($nuevo);
        $this->assertContainsOnlyInstancesOf(\src\configuracion\domain\entity\App::class, $nuevo);
    }

    public function test_modulos_instalados_y_config_schema_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(ModuloInstaladoRepositoryInterface::class);
        $instalados = $repo->matching(Criteria::create()->orderBy(ModuloInstaladoField::IdMod));
        $this->assertContainsOnlyInstancesOf(\src\configuracion\domain\entity\ModuloInstalado::class, $instalados);

        $config = $GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class);
        $schemas = $config->matching(Criteria::create()->orderBy(ConfigSchemaField::Parametro));
        $this->assertContainsOnlyInstancesOf(\src\configuracion\domain\entity\ConfigSchema::class, $schemas);
    }
}

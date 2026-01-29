<?php

namespace Tests\factories\menus;

use Faker\Factory;
use src\menus\domain\entity\MetaMenu;
use src\menus\domain\value_objects\MetaMenuUrl;
use src\menus\domain\value_objects\MetaMenuParametros;
use src\menus\domain\value_objects\MetaMenuDescripcion;

/**
 * Factory para crear instancias de MetaMenu para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class MetaMenuFactory
{
    private int $count = 1;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Crea una instancia simple de MetaMenu con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): MetaMenu
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oMetaMenu = new MetaMenu();
        $oMetaMenu->setId_metamenu($id);

        $oMetaMenu->setUrlVo(new MetaMenuUrl('test_url_vo'));
        $oMetaMenu->setParametrosVo(new MetaMenuParametros('test_parametros_vo'));
        $oMetaMenu->setDescripcionVo(new MetaMenuDescripcion('test_descripcion_vo'));

        return $oMetaMenu;
    }

    /**
     * Crea una instancia de MetaMenu con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return MetaMenu
     */
    public function create(?int $id = null): MetaMenu
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oMetaMenu = new MetaMenu();
        $oMetaMenu->setId_metamenu($id);

        $oMetaMenu->setId_mod($faker->numberBetween(1, 1000));
        $oMetaMenu->setUrlVo(new MetaMenuUrl($faker->url));
        $oMetaMenu->setParametrosVo(new MetaMenuParametros($faker->word));
        $oMetaMenu->setDescripcionVo(new MetaMenuDescripcion($faker->sentence));

        return $oMetaMenu;
    }

    /**
     * Crea múltiples instancias de MetaMenu
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ?? (9900000 + random_int(1000, 9999));
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}

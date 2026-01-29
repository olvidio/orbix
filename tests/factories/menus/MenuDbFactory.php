<?php

namespace Tests\factories\menus;

use Faker\Factory;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\value_objects\MenuName;
use src\menus\domain\value_objects\MenuParametros;

/**
 * Factory para crear instancias de MenuDb para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class MenuDbFactory
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
     * Crea una instancia simple de MenuDb con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): MenuDb
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oMenuDb = new MenuDb();
        $oMenuDb->setId_menu($id);

        $oMenuDb->setMenuVo(new MenuName('test_menu_vo'));
        $oMenuDb->setParametrosVo(new MenuParametros('test_parametros_vo'));

        return $oMenuDb;
    }

    /**
     * Crea una instancia de MenuDb con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return MenuDb
     */
    public function create(?int $id = null): MenuDb
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oMenuDb = new MenuDb();
        $oMenuDb->setId_menu($id);

        $oMenuDb->setOrden($faker->numberBetween(1, 100));
        $oMenuDb->setMenuVo(new MenuName($faker->word));
        $oMenuDb->setParametrosVo(new MenuParametros($faker->word));
        $oMenuDb->setId_metamenu($faker->numberBetween(1, 1000));
        $oMenuDb->setMenu_perm($faker->numberBetween(1, 1000));
        $oMenuDb->setId_grupmenu($faker->numberBetween(1, 1000));
        $oMenuDb->setOk($faker->boolean);

        return $oMenuDb;
    }

    /**
     * Crea múltiples instancias de MenuDb
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

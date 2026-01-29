<?php

namespace Tests\factories\menus;

use Faker\Factory;
use src\menus\domain\entity\GrupMenu;
use src\menus\domain\value_objects\GrupMenuName;

/**
 * Factory para crear instancias de GrupMenu para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class GrupMenuFactory
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
     * Crea una instancia simple de GrupMenu con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): GrupMenu
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oGrupMenu = new GrupMenu();
        $oGrupMenu->setId_grupmenu($id);

        $oGrupMenu->setGrupMenuVo(new GrupMenuName('test_grup_menu_vo'));

        return $oGrupMenu;
    }

    /**
     * Crea una instancia de GrupMenu con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return GrupMenu
     */
    public function create(?int $id = null): GrupMenu
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oGrupMenu = new GrupMenu();
        $oGrupMenu->setId_grupmenu($id);

        $oGrupMenu->setGrupMenuVo(new GrupMenuName($faker->word));
        $oGrupMenu->setOrden($faker->numberBetween(1, 100));

        return $oGrupMenu;
    }

    /**
     * Crea múltiples instancias de GrupMenu
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

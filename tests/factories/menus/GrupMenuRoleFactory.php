<?php

namespace Tests\factories\menus;

use Faker\Factory;
use src\menus\domain\entity\GrupMenuRole;

/**
 * Factory para crear instancias de GrupMenuRole para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class GrupMenuRoleFactory
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
     * Crea una instancia simple de GrupMenuRole con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): GrupMenuRole
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oGrupMenuRole = new GrupMenuRole();
        $oGrupMenuRole->setId_item($id);

        $oGrupMenuRole->setId_grupmenu(11);
        $oGrupMenuRole->setId_role(22);

        return $oGrupMenuRole;
    }

    /**
     * Crea una instancia de GrupMenuRole con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return GrupMenuRole
     */
    public function create(?int $id = null): GrupMenuRole
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oGrupMenuRole = new GrupMenuRole();
        $oGrupMenuRole->setId_item($id);

        $oGrupMenuRole->setId_grupmenu($faker->numberBetween(1, 1000));
        $oGrupMenuRole->setId_role($faker->numberBetween(1, 1000));

        return $oGrupMenuRole;
    }

    /**
     * Crea múltiples instancias de GrupMenuRole
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

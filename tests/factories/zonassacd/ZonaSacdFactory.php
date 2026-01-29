<?php

namespace Tests\factories\zonassacd;

use Faker\Factory;
use src\zonassacd\domain\entity\ZonaSacd;

/**
 * Factory para crear instancias de ZonaSacd para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ZonaSacdFactory
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
     * Crea una instancia simple de ZonaSacd con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ZonaSacd
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oZonaSacd = new ZonaSacd();
        $oZonaSacd->setId_item($id);

        $oZonaSacd->setId_nom(1);
        $oZonaSacd->setId_zona(1);
        $oZonaSacd->setPropia(false);

        return $oZonaSacd;
    }

    /**
     * Crea una instancia de ZonaSacd con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ZonaSacd
     */
    public function create(?int $id = null): ZonaSacd
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oZonaSacd = new ZonaSacd();
        $oZonaSacd->setId_item($id);

        $oZonaSacd->setId_nom($faker->numberBetween(1, 1000));
        $oZonaSacd->setId_zona($faker->numberBetween(1, 1000));
        $oZonaSacd->setPropia($faker->boolean);
        $oZonaSacd->setDw1($faker->boolean);
        $oZonaSacd->setDw2($faker->boolean);
        $oZonaSacd->setDw3($faker->boolean);
        $oZonaSacd->setDw4($faker->boolean);
        $oZonaSacd->setDw5($faker->boolean);
        $oZonaSacd->setDw6($faker->boolean);
        $oZonaSacd->setDw7($faker->boolean);

        return $oZonaSacd;
    }

    /**
     * Crea múltiples instancias de ZonaSacd
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

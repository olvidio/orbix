<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\Region;
use src\ubis\domain\value_objects\RegionName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\RegionId;

/**
 * Factory para crear instancias de Region para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class RegionFactory
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
     * Crea una instancia simple de Region con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(): Region
    {
        $id = 9000 + random_int(1000, 9999);
        $faker = Factory::create('es_ES');
        $oRegion = new Region();
        $oRegion->setId_region($id);
        $oRegion->setRegionVo(RegionCode::fromNullableString(substr($faker->word, 0, 6)));


        return $oRegion;
    }

    /**
     * Crea una instancia de Region con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Region
     */
    public function create(): Region
    {
        $faker = Factory::create('es_ES');
        $id = 9000 + random_int(1000, 9999);

        $oRegion = new Region();
        $oRegion->setRegionVo(new RegionCode($faker->word));

        $oRegion->setId_region($id);
        $oRegion->setNombreRegionVo(new RegionNameText($faker->name));
        $oRegion->setActive($faker->boolean);

        return $oRegion;
    }

    /**
     * Crea múltiples instancias de Region
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count): array
    {
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create();
        }

        return $instances;
    }
}

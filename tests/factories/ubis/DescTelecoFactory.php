<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\DescTeleco;
use src\ubis\domain\value_objects\DescTelecoOrder;
use src\ubis\domain\value_objects\DescTelecoText;

/**
 * Factory para crear instancias de DescTeleco para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class DescTelecoFactory
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
     * Crea una instancia simple de DescTeleco con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): DescTeleco
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oDescTeleco = new DescTeleco();
        $oDescTeleco->setId_item($id);

        $oDescTeleco->setId_tipo_teleco(8);

        return $oDescTeleco;
    }

    /**
     * Crea una instancia de DescTeleco con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return DescTeleco
     */
    public function create(?int $id = null): DescTeleco
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oDescTeleco = new DescTeleco();
        $oDescTeleco->setId_item($id);

        $oDescTeleco->setOrdenVo(new DescTelecoOrder($faker->numberBetween(1, 10)));
        $oDescTeleco->setId_tipo_teleco($faker->numberBetween(1, 10));
        $oDescTeleco->setDescTelecoVo(new DescTelecoText(substr($faker->word(), 0, 20)));
        $oDescTeleco->setUbi($faker->boolean);
        $oDescTeleco->setPersona($faker->boolean);

        return $oDescTeleco;
    }

    /**
     * Crea múltiples instancias de DescTeleco
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

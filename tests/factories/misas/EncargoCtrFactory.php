<?php

namespace Tests\factories\misas;

use Faker\Factory;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;
use src\tablonanuncios\domain\value_objects\AnuncioId;

/**
 * Factory para crear instancias de EncargoCtr para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoCtrFactory
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
     * Crea una instancia simple de EncargoCtr con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): EncargoCtr
    {
        $uuid_itemVo = $id ?? EncargoCtrId::random();
        $oEncargoCtr = new EncargoCtr();
        $oEncargoCtr->setUuidItemVo($uuid_itemVo);

        $oEncargoCtr->setId_enc(100123331);
        $oEncargoCtr->setId_ubi(1000);

        return $oEncargoCtr;
    }

    /**
     * Crea una instancia de EncargoCtr con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoCtr
     */
    public function create(?string $id = null): EncargoCtr
    {
        $faker = Factory::create('es_ES');
        $uuid_itemVo = $id ?? EncargoCtrId::random();
        $oEncargoCtr = new EncargoCtr();
        $oEncargoCtr->setUuidItemVo($uuid_itemVo);

        $oEncargoCtr->setId_enc($faker->numberBetween(1, 100));
        $oEncargoCtr->setId_ubi($faker->numberBetween(1001234, 1000000));

        return $oEncargoCtr;
    }

    /**
     * Crea múltiples instancias de EncargoCtr
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

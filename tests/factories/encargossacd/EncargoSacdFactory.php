<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de EncargoSacd para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoSacdFactory
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
     * Crea una instancia simple de EncargoSacd con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoSacd
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_item($id);

        $oEncargoSacd->setId_enc(3);
        $oEncargoSacd->setId_nom(10011);
        $oEncargoSacd->setModoVo(new EncargoModoId(3));
        $oEncargoSacd->setF_ini(new DateTimeLocal('2023-04-05'));

        return $oEncargoSacd;
    }

    /**
     * Crea una instancia de EncargoSacd con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoSacd
     */
    public function create(?int $id = null): EncargoSacd
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoSacd = new EncargoSacd();
        $oEncargoSacd->setId_item($id);

        $oEncargoSacd->setId_enc($faker->numberBetween(1, 100));
        $oEncargoSacd->setId_nom($faker->numberBetween(10011, 100000));
        $oEncargoSacd->setModoVo(new EncargoModoId($faker->numberBetween(1, 10)));
        $oEncargoSacd->setF_ini(new DateTimeLocal($faker->date()));
        $oEncargoSacd->setF_fin(new DateTimeLocal($faker->date()));

        return $oEncargoSacd;
    }

    /**
     * Crea múltiples instancias de EncargoSacd
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

<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoSacdObserv;
use src\encargossacd\domain\value_objects\ObservText;

/**
 * Factory para crear instancias de EncargoSacdObserv para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoSacdObservFactory
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
     * Crea una instancia simple de EncargoSacdObserv con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoSacdObserv
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoSacdObserv = new EncargoSacdObserv();
        $oEncargoSacdObserv->setId_item($id);

        $oEncargoSacdObserv->setId_nom(10011);
        $oEncargoSacdObserv->setObservVo(new ObservText('test_observ_vo'));

        return $oEncargoSacdObserv;
    }

    /**
     * Crea una instancia de EncargoSacdObserv con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoSacdObserv
     */
    public function create(?int $id = null): EncargoSacdObserv
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoSacdObserv = new EncargoSacdObserv();
        $oEncargoSacdObserv->setId_item($id);

        $oEncargoSacdObserv->setId_nom($faker->numberBetween(10011, 100000));
        $oEncargoSacdObserv->setObservVo(new ObservText($faker->word));

        return $oEncargoSacdObserv;
    }

    /**
     * Crea múltiples instancias de EncargoSacdObserv
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

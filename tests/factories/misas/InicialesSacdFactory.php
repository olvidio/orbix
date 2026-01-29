<?php

namespace Tests\factories\misas;

use Faker\Factory;
use src\misas\domain\entity\InicialesSacd;

/**
 * Factory para crear instancias de InicialesSacd para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class InicialesSacdFactory
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
     * Crea una instancia simple de InicialesSacd con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): InicialesSacd
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oInicialesSacd = new InicialesSacd();
        $oInicialesSacd->setId_nom($id);


        return $oInicialesSacd;
    }

    /**
     * Crea una instancia de InicialesSacd con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return InicialesSacd
     */
    public function create(?int $id = null): InicialesSacd
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oInicialesSacd = new InicialesSacd();
        $oInicialesSacd->setId_nom($id);

        $oInicialesSacd->setIniciales($faker->word);
        $oInicialesSacd->setColor($faker->word);

        return $oInicialesSacd;
    }

    /**
     * Crea múltiples instancias de InicialesSacd
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

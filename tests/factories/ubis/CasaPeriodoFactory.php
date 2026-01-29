<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\CasaPeriodo;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\SfsvOtrosId;

/**
 * Factory para crear instancias de CasaPeriodo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CasaPeriodoFactory
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
     * Crea una instancia simple de CasaPeriodo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CasaPeriodo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCasaPeriodo = new CasaPeriodo();
        $oCasaPeriodo->setId_item($id);

        $oCasaPeriodo->setId_ubi(1);

        return $oCasaPeriodo;
    }

    /**
     * Crea una instancia de CasaPeriodo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CasaPeriodo
     */
    public function create(?int $id = null): CasaPeriodo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCasaPeriodo = new CasaPeriodo();
        $oCasaPeriodo->setId_item($id);

        $oCasaPeriodo->setId_ubi($faker->numberBetween(1, 1000));
        $oCasaPeriodo->setF_ini(new DateTimeLocal($faker->date()));
        $oCasaPeriodo->setF_fin(new DateTimeLocal($faker->date()));
        $oCasaPeriodo->setSfsvVo(new SfsvOtrosId($faker->numberBetween(1, 3)));

        return $oCasaPeriodo;
    }

    /**
     * Crea múltiples instancias de CasaPeriodo
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

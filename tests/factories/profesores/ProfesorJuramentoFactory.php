<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorJuramento;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorJuramento para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorJuramentoFactory
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
     * Crea una instancia simple de ProfesorJuramento con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorJuramento
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorJuramento = new ProfesorJuramento();
        $oProfesorJuramento->setId_item($id);

        $oProfesorJuramento->setId_nom(1);
        $oProfesorJuramento->setF_juramento(new DateTimeLocal('2024-01-01'));

        return $oProfesorJuramento;
    }

    /**
     * Crea una instancia de ProfesorJuramento con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorJuramento
     */
    public function create(?int $id = null): ProfesorJuramento
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorJuramento = new ProfesorJuramento();
        $oProfesorJuramento->setId_item($id);

        $oProfesorJuramento->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorJuramento->setF_juramento(new DateTimeLocal($faker->date()));

        return $oProfesorJuramento;
    }

    /**
     * Crea múltiples instancias de ProfesorJuramento
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

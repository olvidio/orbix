<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorLatin;

/**
 * Factory para crear instancias de ProfesorLatin para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorLatinFactory
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
     * Crea una instancia simple de ProfesorLatin con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorLatin
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorLatin = new ProfesorLatin();
        $oProfesorLatin->setId_nom($id);

        $oProfesorLatin->setLatin(false);

        return $oProfesorLatin;
    }

    /**
     * Crea una instancia de ProfesorLatin con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorLatin
     */
    public function create(?int $id = null): ProfesorLatin
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorLatin = new ProfesorLatin();
        $oProfesorLatin->setId_nom($id);

        $oProfesorLatin->setLatin($faker->boolean);

        return $oProfesorLatin;
    }

    /**
     * Crea múltiples instancias de ProfesorLatin
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

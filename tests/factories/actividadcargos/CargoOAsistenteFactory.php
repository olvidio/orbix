<?php

namespace Tests\factories\actividadcargos;

use Faker\Factory;
use src\actividadcargos\domain\entity\CargoOAsistente;

/**
 * Factory para crear instancias de CargoOAsistente para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CargoOAsistenteFactory
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
     * Crea una instancia simple de CargoOAsistente con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CargoOAsistente
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCargoOAsistente = new CargoOAsistente();
        $oCargoOAsistente->setId_activ($id);

        $oCargoOAsistente->setId_nom(1);
        $oCargoOAsistente->setPropio(false);
        $oCargoOAsistente->setId_cargo(1);

        return $oCargoOAsistente;
    }

    /**
     * Crea una instancia de CargoOAsistente con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CargoOAsistente
     */
    public function create(?int $id = null): CargoOAsistente
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCargoOAsistente = new CargoOAsistente();
        $oCargoOAsistente->setId_activ($id);

        $oCargoOAsistente->setId_nom($faker->numberBetween(1, 1000));
        $oCargoOAsistente->setPropio($faker->boolean);
        $oCargoOAsistente->setId_cargo($faker->numberBetween(1, 1000));

        return $oCargoOAsistente;
    }

    /**
     * Crea múltiples instancias de CargoOAsistente
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

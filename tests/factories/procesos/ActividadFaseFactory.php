<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\value_objects\FaseId;

/**
 * Factory para crear instancias de ActividadFase para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadFaseFactory
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
     * Crea una instancia simple de ActividadFase con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadFase
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadFase = new ActividadFase();
        $oActividadFase->setId_fase($id);

        $oActividadFase->setSf(false);
        $oActividadFase->setSv(false);

        return $oActividadFase;
    }

    /**
     * Crea una instancia de ActividadFase con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadFase
     */
    public function create(?int $id = null): ActividadFase
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadFase = new ActividadFase();
        $oActividadFase->setId_fase($id);

        $oActividadFase->setDesc_fase($faker->word);
        $oActividadFase->setSf($faker->boolean);
        $oActividadFase->setSv($faker->boolean);

        return $oActividadFase;
    }

    /**
     * Crea múltiples instancias de ActividadFase
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

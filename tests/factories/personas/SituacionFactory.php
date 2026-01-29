<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\personas\domain\entity\Situacion;
use src\personas\domain\value_objects\SituacionCode;
use src\personas\domain\value_objects\SituacionName;

/**
 * Factory para crear instancias de Situacion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class SituacionFactory
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
     * Crea una instancia simple de Situacion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Situacion
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oSituacion = new Situacion();
        $oSituacion->setSituacionVo($id);


        return $oSituacion;
    }

    /**
     * Crea una instancia de Situacion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Situacion
     */
    public function create(?int $id = null): Situacion
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oSituacion = new Situacion();
        $oSituacion->setSituacionVo($id);

        $oSituacion->setNombreSituacionVo(new SituacionName($faker->randomLetter()));
        $oSituacion->setNombre_situacion($faker->name);

        return $oSituacion;
    }

    /**
     * Crea múltiples instancias de Situacion
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

<?php

namespace Tests\factories\actividades;

use Faker\Factory;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\RepeticionText;
use src\actividades\domain\value_objects\TemporadaCode;
use src\actividades\domain\value_objects\RepeticionTipo;

/**
 * Factory para crear instancias de Repeticion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class RepeticionFactory
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
     * Crea una instancia simple de Repeticion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Repeticion
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oRepeticion = new Repeticion();
        $oRepeticion->setId_repeticion($id);

        $oRepeticion->setRepeticionVo(new RepeticionText('test_repeticion_vo'));
        $oRepeticion->setTemporadaVo(new TemporadaCode('A'));
        $oRepeticion->setTipoRepeticionVo(new RepeticionTipo(2));

        return $oRepeticion;
    }

    /**
     * Crea una instancia de Repeticion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Repeticion
     */
    public function create(?int $id = null): Repeticion
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oRepeticion = new Repeticion();
        $oRepeticion->setId_repeticion($id);

        $oRepeticion->setRepeticionVo(new RepeticionText($faker->word));
        $oRepeticion->setTemporadaVo(new TemporadaCode($faker->randomLetter));
        $oRepeticion->setTipoRepeticionVo(new RepeticionTipo($faker->numberBetween(1, 3)));

        return $oRepeticion;
    }

    /**
     * Crea múltiples instancias de Repeticion
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

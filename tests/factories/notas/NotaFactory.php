<?php

namespace Tests\factories\notas;

use Faker\Factory;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\Descripcion;
use src\notas\domain\value_objects\Breve;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Factory para crear instancias de Nota para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class NotaFactory
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
     * Crea una instancia simple de Nota con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Nota
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? $faker->numberBetween(1, 13);
        $oNota = new Nota();
        $oNota->setId_situacion($id);

        $oNota->setDescripcionVo(new Descripcion('test_descripcion_vo'));
        $oNota->setSuperada(false);

        return $oNota;
    }

    /**
     * Crea una instancia de Nota con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Nota
     */
    public function create(?int $id = null): Nota
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? $faker->numberBetween(1, 13);

        $oNota = new Nota();
        $oNota->setId_situacion($id);

        $oNota->setDescripcionVo(new Descripcion($faker->sentence));
        $oNota->setSuperada($faker->boolean);
        $oNota->setBreveVo(new Breve($faker->word));

        return $oNota;
    }

    /**
     * Crea múltiples instancias de Nota
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

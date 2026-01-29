<?php

namespace Tests\factories\misas;

use Faker\Factory;
use src\misas\domain\entity\Plantilla;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Factory para crear instancias de Plantilla para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PlantillaFactory
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
     * Crea una instancia simple de Plantilla con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Plantilla
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPlantilla = new Plantilla();
        $oPlantilla->setId_item($id);

        $oPlantilla->setId_ctr(1);
        $oPlantilla->setTarea(1);
        $oPlantilla->setDia('test_dia');
        $oPlantilla->setT_start(TimeLocal::fromString('12:00'));
        $oPlantilla->setT_end(TimeLocal::fromString('15:30'));

        return $oPlantilla;
    }

    /**
     * Crea una instancia de Plantilla con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Plantilla
     */
    public function create(?int $id = null): Plantilla
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPlantilla = new Plantilla();
        $oPlantilla->setId_item($id);

        $oPlantilla->setId_ctr($faker->numberBetween(1, 1000));
        $oPlantilla->setTarea($faker->numberBetween(1, 1000));
        $oPlantilla->setDia($faker->word);
        $oPlantilla->setSemana($faker->numberBetween(1, 1000));
        $oPlantilla->setT_start(new DateTimeLocal($faker->word));
        $oPlantilla->setT_end(new DateTimeLocal($faker->word));
        $oPlantilla->setId_nom($faker->numberBetween(1, 1000));
        $oPlantilla->setObserv($faker->word);

        return $oPlantilla;
    }

    /**
     * Crea múltiples instancias de Plantilla
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

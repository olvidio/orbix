<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\procesos\domain\entity\ActividadTarea;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\TareaId;

/**
 * Factory para crear instancias de ActividadTarea para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadTareaFactory
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
     * Crea una instancia simple de ActividadTarea con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadTarea
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadTarea = new ActividadTarea();
        $oActividadTarea->setId_tarea($id);

        $oActividadTarea->setIdFaseVo(new FaseId(34));

        return $oActividadTarea;
    }

    /**
     * Crea una instancia de ActividadTarea con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadTarea
     */
    public function create(?int $id = null): ActividadTarea
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadTarea = new ActividadTarea();
        $oActividadTarea->setId_tarea($id);

        $oActividadTarea->setIdFaseVo(new FaseId($faker->numberBetween(1, 10)));
        $oActividadTarea->setDesc_tarea(substr($faker->word, 0, 70));

        return $oActividadTarea;
    }

    /**
     * Crea múltiples instancias de ActividadTarea
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

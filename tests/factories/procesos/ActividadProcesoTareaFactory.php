<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\value_objects\ActividadId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use src\procesos\domain\value_objects\TareaObserv;

/**
 * Factory para crear instancias de ActividadProcesoTarea para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadProcesoTareaFactory
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
     * Crea una instancia simple de ActividadProcesoTarea con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadProcesoTarea
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadProcesoTarea = new ActividadProcesoTarea();
        $oActividadProcesoTarea->setId_item($id);

        $oActividadProcesoTarea->setIdTipoProcesoVo(new ProcesoTipoId(4));
        $oActividadProcesoTarea->setIdActividadVo(new ActividadId(1001));

        return $oActividadProcesoTarea;
    }

    /**
     * Crea una instancia de ActividadProcesoTarea con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadProcesoTarea
     */
    public function create(?int $id = null): ActividadProcesoTarea
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadProcesoTarea = new ActividadProcesoTarea();
        $oActividadProcesoTarea->setId_item($id);

        $oActividadProcesoTarea->setIdTipoProcesoVo(new ProcesoTipoId($faker->numberBetween(1, 10)));
        $oActividadProcesoTarea->setIdActividadVo(new ActividadId($faker->numberBetween(1000, 10000)));
        $oActividadProcesoTarea->setIdFaseVo(new FaseId($faker->numberBetween(1, 10)));
        $oActividadProcesoTarea->setIdTareaVo(new TareaId($faker->numberBetween(1, 10)));
        $oActividadProcesoTarea->setCompletado($faker->boolean);
        $oActividadProcesoTarea->setObservVo(new TareaObserv($faker->word));

        return $oActividadProcesoTarea;
    }

    /**
     * Crea múltiples instancias de ActividadProcesoTarea
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

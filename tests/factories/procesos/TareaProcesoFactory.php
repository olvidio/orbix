<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\procesos\domain\entity\TareaProceso;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;

/**
 * Factory para crear instancias de TareaProceso para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TareaProcesoFactory
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
     * Crea una instancia simple de TareaProceso con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TareaProceso
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTareaProceso = new TareaProceso();
        $oTareaProceso->setId_item($id);

        $oTareaProceso->setIdTipoProcesoVo(new ProcesoTipoId(99));
        $oTareaProceso->setIdFaseVo(new FaseId(3));
        $oTareaProceso->setIdTareaVo(new TareaId(0));
        $oTareaProceso->setStatusVo(new StatusId(2));

        return $oTareaProceso;
    }

    /**
     * Crea una instancia de TareaProceso con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TareaProceso
     */
    public function create(?int $id = null): TareaProceso
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTareaProceso = new TareaProceso();
        $oTareaProceso->setId_item($id);

        $oTareaProceso->setIdTipoProcesoVo(new ProcesoTipoId($faker->numberBetween(1, 100)));
        $oTareaProceso->setIdFaseVo(new FaseId($faker->numberBetween(1, 30)));
        $oTareaProceso->setIdTareaVo(new TareaId($faker->numberBetween(1, 70)));
        $oTareaProceso->setStatusVo(new StatusId($faker->numberBetween(1,4)));
        $oTareaProceso->setId_of_responsable($faker->numberBetween(1, 1000));
        $oTareaProceso->setJson_fases_previas(json_encode([["id_fase"=>"2","id_tarea"=>"","mensaje"=>""]]));


        return $oTareaProceso;
    }

    /**
     * Crea múltiples instancias de TareaProceso
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

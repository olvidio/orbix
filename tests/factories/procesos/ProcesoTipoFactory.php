<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\procesos\domain\entity\ProcesoTipo;
use src\procesos\domain\value_objects\ProcesoTipoId;

/**
 * Factory para crear instancias de ProcesoTipo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProcesoTipoFactory
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
     * Crea una instancia simple de ProcesoTipo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProcesoTipo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProcesoTipo = new ProcesoTipo();
        $oProcesoTipo->setId_tipo_proceso($id);


        return $oProcesoTipo;
    }

    /**
     * Crea una instancia de ProcesoTipo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProcesoTipo
     */
    public function create(?int $id = null): ProcesoTipo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProcesoTipo = new ProcesoTipo();
        $oProcesoTipo->setId_tipo_proceso($id);

        $oProcesoTipo->setNom_proceso($faker->word);
        $oProcesoTipo->setSfsv($faker->numberBetween(1, 1000));

        return $oProcesoTipo;
    }

    /**
     * Crea múltiples instancias de ProcesoTipo
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

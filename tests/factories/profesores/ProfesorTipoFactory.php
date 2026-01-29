<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorTipo;
use src\profesores\domain\value_objects\ProfesorTipoId;
use src\profesores\domain\value_objects\ProfesorTipoName;

/**
 * Factory para crear instancias de ProfesorTipo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorTipoFactory
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
     * Crea una instancia simple de ProfesorTipo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorTipo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorTipo = new ProfesorTipo();
        $oProfesorTipo->setId_tipo_profesor($id);


        return $oProfesorTipo;
    }

    /**
     * Crea una instancia de ProfesorTipo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorTipo
     */
    public function create(?int $id = null): ProfesorTipo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorTipo = new ProfesorTipo();
        $oProfesorTipo->setId_tipo_profesor($id);

        $oProfesorTipo->setTipoProfesorVo(new ProfesorTipoName(substr($faker->word, 0, 50)));

        return $oProfesorTipo;
    }

    /**
     * Crea múltiples instancias de ProfesorTipo
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

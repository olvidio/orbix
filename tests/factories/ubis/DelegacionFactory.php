<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\DelegacionName;
use src\ubis\domain\value_objects\DelegacionGrupoEstudios;
use src\ubis\domain\value_objects\DelegacionRegionStgr;

/**
 * Factory para crear instancias de Delegacion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class DelegacionFactory
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
     * Crea una instancia simple de Delegacion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Delegacion
    {
        $id = $id ?? (9000 + random_int(1000, 9999));
        $faker = Factory::create('es_ES');
        $oDelegacion = new Delegacion();
        $oDelegacion->setIdDlVo(new DelegacionId($id));

        $oDelegacion->setDlVo(new DelegacionCode(substr($faker->word, 0, 6)));
        $oDelegacion->setRegionVo(new RegionCode(substr($faker->word, 0, 6)));
        $oDelegacion->setActive(false);

        return $oDelegacion;
    }

    /**
     * Crea una instancia de Delegacion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Delegacion
     */
    public function create(?int $id = null): Delegacion
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9000 + random_int(1000, 9999));

        $oDelegacion = new Delegacion();
        $oDelegacion->setIdDlVo(new DelegacionId($id));

        $oDelegacion->setDlVo(new DelegacionCode(substr($faker->word, 0, 6)));
        $oDelegacion->setRegionVo(new RegionCode(substr($faker->word, 0, 6)));
        $oDelegacion->setNombreDlVo(new DelegacionName($faker->name));
        $oDelegacion->setGrupoEstudiosVo(new DelegacionGrupoEstudios($faker->word));
        $oDelegacion->setRegionStgrVo(new DelegacionRegionStgr($faker->word));
        $oDelegacion->setActive($faker->boolean);

        return $oDelegacion;
    }

    /**
     * Crea múltiples instancias de Delegacion
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

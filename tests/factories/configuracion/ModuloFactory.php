<?php

namespace Tests\factories\configuracion;

use Faker\Factory;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModuloDescription;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;
use src\configuracion\domain\value_objects\ModsReq;

/**
 * Factory para crear instancias de Modulo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ModuloFactory
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
     * Crea una instancia simple de Modulo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Modulo
    {
        $id = $id ?? random_int(1, 32767);
        $oModulo = new Modulo();
        $oModulo->setId_mod($id);

        $oModulo->setNomVo(new ModuloName('test_nom_vo'));

        return $oModulo;
    }

    /**
     * Crea una instancia de Modulo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Modulo
     */
    public function create(?int $id = null): Modulo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? random_int(1, 32767);

        $oModulo = new Modulo();
        $oModulo->setId_mod($id);

        $oModulo->setNomVo(new ModuloName($faker->word));
        $oModulo->setDescripcionVo(new ModuloDescription($faker->sentence));
        $oModulo->setModsReqVo(new ModsReq($faker->shuffleArray([1,3,5,6,8,9,32,12,24])));
        $oModulo->setAppsReqVo(new AppsReq($faker->shuffleArray([1,3,5,6,8,9,32,12,24])));

        return $oModulo;
    }

    /**
     * Crea múltiples instancias de Modulo
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ??  random_int(1, 3767);
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}

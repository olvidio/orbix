<?php

namespace Tests\factories\actividades;

use Faker\Factory;
use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\NivelStgrDesc;
use src\actividades\domain\value_objects\NivelStgrBreve;
use src\actividades\domain\value_objects\NivelStgrOrden;

/**
 * Factory para crear instancias de NivelStgr para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class NivelStgrFactory
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
     * Crea una instancia simple de NivelStgr con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): NivelStgr
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oNivelStgr = new NivelStgr();
        $oNivelStgr->setNivelStgrVo($id);

        $oNivelStgr->setDescNivelVo(new NivelStgrDesc('test_desc_nivel_vo'));

        return $oNivelStgr;
    }

    /**
     * Crea una instancia de NivelStgr con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return NivelStgr
     */
    public function create(?int $id = null): NivelStgr
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oNivelStgr = new NivelStgr();
        $oNivelStgr->setNivelStgrVo($id);

        $oNivelStgr->setDescNivelVo(new NivelStgrDesc($faker->word));
        $oNivelStgr->setDescBreveVo(new NivelStgrBreve($faker->word));
        $oNivelStgr->setOrdenVo(new NivelStgrOrden($faker->numberBetween(1, 10)));

        return $oNivelStgr;
    }

    /**
     * Crea múltiples instancias de NivelStgr
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

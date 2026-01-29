<?php

namespace Tests\factories\casas;

use Faker\Factory;
use src\casas\domain\entity\Ingreso;
use src\casas\domain\value_objects\IngresoImporte;
use src\casas\domain\value_objects\IngresoNumAsistentes;
use src\casas\domain\value_objects\IngresoObserv;

/**
 * Factory para crear instancias de Ingreso para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class IngresoFactory
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
     * Crea una instancia simple de Ingreso con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Ingreso
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oIngreso = new Ingreso();
        $oIngreso->setId_activ($id);


        return $oIngreso;
    }

    /**
     * Crea una instancia de Ingreso con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Ingreso
     */
    public function create(?int $id = null): Ingreso
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oIngreso = new Ingreso();
        $oIngreso->setId_activ($id);

        $oIngreso->setIngresosVo(new IngresoImporte($faker->randomFloat(2, 0, 100)));
        $oIngreso->setNumAsistentesVo(new IngresoNumAsistentes($faker->numberBetween(1, 10)));
        $oIngreso->setIngresosPrevistosVo(new IngresoImporte($faker->randomFloat(2, 0, 100)));
        $oIngreso->setNumAsistentesPrevistosVo(new IngresoNumAsistentes($faker->numberBetween(1, 10)));
        $oIngreso->setObservVo(new IngresoObserv($faker->word));

        return $oIngreso;
    }

    /**
     * Crea múltiples instancias de Ingreso
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

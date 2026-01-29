<?php

namespace Tests\factories\actividadcargos;

use Faker\Factory;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\value_objects\ObservacionesCargo;

/**
 * Factory para crear instancias de ActividadCargo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadCargoFactory
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
     * Crea una instancia simple de ActividadCargo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadCargo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($id);

        $oActividadCargo->setId_activ(3001145);
        $oActividadCargo->setId_cargo(3);
        $oActividadCargo->setPuede_agd(false);

        return $oActividadCargo;
    }

    /**
     * Crea una instancia de ActividadCargo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadCargo
     */
    public function create(?int $id = null): ActividadCargo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($id);

        $oActividadCargo->setId_activ($faker->numberBetween(3001145, 300000));
        $oActividadCargo->setId_cargo($faker->numberBetween(1, 10));
        $oActividadCargo->setId_nom($faker->numberBetween(10011, 100000));
        $oActividadCargo->setPuede_agd($faker->boolean);
        $oActividadCargo->setObservVo(new ObservacionesCargo($faker->text(50)));

        return $oActividadCargo;
    }

    /**
     * Crea múltiples instancias de ActividadCargo
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

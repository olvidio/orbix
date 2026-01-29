<?php

namespace Tests\factories\actividadcargos;

use Faker\Factory;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividadcargos\domain\value_objects\OrdenCargo;
use src\actividadcargos\domain\value_objects\TipoCargoCode;

/**
 * Factory para crear instancias de Cargo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CargoFactory
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
     * Crea una instancia simple de Cargo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Cargo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCargo = new Cargo();
        $oCargo->setId_cargo($id);

        $oCargo->setCargoVo(new CargoCode('sd44'));

        return $oCargo;
    }

    /**
     * Crea una instancia de Cargo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Cargo
     */
    public function create(?int $id = null): Cargo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCargo = new Cargo();
        $oCargo->setId_cargo($id);

        $oCargo->setCargoVo(new CargoCode(substr($faker->word, 0, 8)));
        $oCargo->setOrdenCargoVo(new OrdenCargo($faker->numberBetween(1, 10)));
        $oCargo->setTipoCargoVo(new TipoCargoCode($faker->randomElement(TipoCargoCode::VALID_VALUES)));
        $oCargo->setSf($faker->boolean);
        $oCargo->setSv($faker->boolean);

        return $oCargo;
    }

    /**
     * Crea múltiples instancias de Cargo
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

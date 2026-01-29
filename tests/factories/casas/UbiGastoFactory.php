<?php

namespace Tests\factories\casas;

use Faker\Factory;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de UbiGasto para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class UbiGastoFactory
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
     * Crea una instancia simple de UbiGasto con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): UbiGasto
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oUbiGasto = new UbiGasto();
        $oUbiGasto->setId_item($id);

        // ubi existente para foreign keys
        $oUbiGasto->setId_ubi(-10019001);
        $oUbiGasto->setF_gasto(new DateTimeLocal('now'));

        return $oUbiGasto;
    }

    /**
     * Crea una instancia de UbiGasto con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return UbiGasto
     */
    public function create(?int $id = null): UbiGasto
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oUbiGasto = new UbiGasto();
        $oUbiGasto->setId_item($id);

        // ubi existente para foreign keys
        $oUbiGasto->setId_ubi(-10019001);
        $oUbiGasto->setF_gasto(new DateTimeLocal($faker->date()));
        $oUbiGasto->setTipoVo(new UbiGastoTipo($faker->numberBetween(1, 10)));
        $oUbiGasto->setCantidadVo(new UbiGastoCantidad($faker->randomFloat(2, 0, 100)));

        return $oUbiGasto;
    }

    /**
     * Crea múltiples instancias de UbiGasto
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

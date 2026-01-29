<?php

namespace Tests\factories\cambios;

use Faker\Factory;
use src\cambios\domain\entity\CambioAnotado;

/**
 * Factory para crear instancias de CambioAnotado para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CambioAnotadoFactory
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
     * Crea una instancia simple de CambioAnotado con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CambioAnotado
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCambioAnotado = new CambioAnotado();
        $oCambioAnotado->setId_item($id);

        $oCambioAnotado->setId_schema_cambio(1);
        $oCambioAnotado->setId_item_cambio(134);
        $oCambioAnotado->setAnotado(false);
        $oCambioAnotado->setServer(1);

        return $oCambioAnotado;
    }

    /**
     * Crea una instancia de CambioAnotado con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CambioAnotado
     */
    public function create(?int $id = null): CambioAnotado
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCambioAnotado = new CambioAnotado();
        $oCambioAnotado->setId_item($id);

        $oCambioAnotado->setId_schema_cambio($faker->numberBetween(1001, 3200));
        $oCambioAnotado->setId_item_cambio($faker->numberBetween(1, 1000));
        $oCambioAnotado->setAnotado($faker->boolean);
        $oCambioAnotado->setServer($faker->numberBetween(1, 2));

        return $oCambioAnotado;
    }

    /**
     * Crea múltiples instancias de CambioAnotado
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

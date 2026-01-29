<?php

namespace Tests\factories\configuracion;

use Faker\Factory;
use src\configuracion\domain\entity\ModuloInstalado;
use src\configuracion\domain\value_objects\ModuloId;

/**
 * Factory para crear instancias de ModuloInstalado para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ModuloInstaladoFactory
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
     * Crea una instancia simple de ModuloInstalado con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ModuloInstalado
    {
        $id = $id ?? random_int(1, 32767);
        $oModuloInstalado = new ModuloInstalado();
        $oModuloInstalado->setId_mod($id);


        return $oModuloInstalado;
    }

    /**
     * Crea una instancia de ModuloInstalado con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ModuloInstalado
     */
    public function create(?int $id = null): ModuloInstalado
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? random_int(1, 32767);

        $oModuloInstalado = new ModuloInstalado();
        $oModuloInstalado->setId_mod($id);

        $oModuloInstalado->setActive($faker->boolean);

        return $oModuloInstalado;
    }

    /**
     * Crea múltiples instancias de ModuloInstalado
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ?? random_int(1, 3767);
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}

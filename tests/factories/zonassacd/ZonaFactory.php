<?php

namespace Tests\factories\zonassacd;

use Faker\Factory;
use src\zonassacd\domain\entity\Zona;
use src\zonassacd\domain\value_objects\NombreZona;

/**
 * Factory para crear instancias de Zona para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ZonaFactory
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
     * Crea una instancia simple de Zona con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Zona
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oZona = new Zona();
        $oZona->setId_zona($id);

        $oZona->setNombreZonaVo(new NombreZona('test_nombre_zona_vo'));
        $oZona->setNombre_zona('test_nombre_zona');

        return $oZona;
    }

    /**
     * Crea una instancia de Zona con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Zona
     */
    public function create(?int $id = null): Zona
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oZona = new Zona();
        $oZona->setId_zona($id);

        $oZona->setNombreZonaVo(new NombreZona($faker->name));
        $oZona->setNombre_zona($faker->name);
        $oZona->setOrden($faker->numberBetween(1, 100));
        $oZona->setId_grupo($faker->numberBetween(1, 1000));
        $oZona->setId_nom($faker->numberBetween(1, 1000));

        return $oZona;
    }

    /**
     * Crea múltiples instancias de Zona
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

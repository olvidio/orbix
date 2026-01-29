<?php

namespace Tests\factories\actividades;

use Faker\Factory;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\value_objects\TipoActivNombre;

/**
 * Factory para crear instancias de TipoDeActividad para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoDeActividadFactory
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
     * Crea una instancia simple de TipoDeActividad con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TipoDeActividad
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTipoDeActividad = new TipoDeActividad();
        $oTipoDeActividad->setId_tipo_activ($id);

        $oTipoDeActividad->setNombreVo(new TipoActivNombre('test_nombre_vo'));

        return $oTipoDeActividad;
    }

    /**
     * Crea una instancia de TipoDeActividad con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TipoDeActividad
     */
    public function create(?int $id = null): TipoDeActividad
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTipoDeActividad = new TipoDeActividad();
        $oTipoDeActividad->setId_tipo_activ($id);

        $oTipoDeActividad->setId_tipo_proceso_ex($faker->numberBetween(1, 1000));
        $oTipoDeActividad->setNombreVo(new TipoActivNombre($faker->name));
        $oTipoDeActividad->setId_tipo_proceso_sv($faker->numberBetween(1, 1000));
        $oTipoDeActividad->setId_tipo_proceso_ex_sv($faker->numberBetween(1, 1000));
        $oTipoDeActividad->setId_tipo_proceso_sf($faker->numberBetween(1, 1000));
        $oTipoDeActividad->setId_tipo_proceso_ex_sf($faker->numberBetween(1, 1000));

        return $oTipoDeActividad;
    }

    /**
     * Crea múltiples instancias de TipoDeActividad
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

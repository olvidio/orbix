<?php

namespace Tests\factories\actividadtarifas;

use Faker\Factory;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;

/**
 * Factory para crear instancias de RelacionTarifaTipoActividad para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class RelacionTarifaTipoActividadFactory
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
     * Crea una instancia simple de RelacionTarifaTipoActividad con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): RelacionTarifaTipoActividad
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oRelacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
        $oRelacionTarifaTipoActividad->setId_item($id);

        $oRelacionTarifaTipoActividad->setIdTarifaVo(new TarifaId(5));
        $oRelacionTarifaTipoActividad->setIdTipoActivVo(new ActividadTipoId(123456));
        $oRelacionTarifaTipoActividad->setIdSerieVo(new SerieId(1));

        return $oRelacionTarifaTipoActividad;
    }

    /**
     * Crea una instancia de RelacionTarifaTipoActividad con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return RelacionTarifaTipoActividad
     */
    public function create(?int $id = null): RelacionTarifaTipoActividad
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oRelacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
        $oRelacionTarifaTipoActividad->setId_item($id);

        $oRelacionTarifaTipoActividad->setIdTarifaVo(new TarifaId($faker->numberBetween(1, 100)));
        $oRelacionTarifaTipoActividad->setIdTipoActivVo(new ActividadTipoId($faker->numerify('######')));
        $oRelacionTarifaTipoActividad->setIdSerieVo(new SerieId($faker->randomKey(SerieId::getArraySerie())));

        return $oRelacionTarifaTipoActividad;
    }

    /**
     * Crea múltiples instancias de RelacionTarifaTipoActividad
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

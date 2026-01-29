<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\TarifaUbi;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\profesores\domain\value_objects\YearNumber;
use src\ubis\domain\value_objects\ObservCasaText;
use src\ubis\domain\value_objects\TarifaCantidad;

/**
 * Factory para crear instancias de TarifaUbi para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TarifaUbiFactory
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
     * Crea una instancia simple de TarifaUbi con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TarifaUbi
    {
        return self::create($id);
    }

    /**
     * Crea una instancia de TarifaUbi con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TarifaUbi
     */
    public function create(?int $id = null): TarifaUbi
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTarifaUbi = new TarifaUbi();
        $oTarifaUbi->setId_item($id);

        $oTarifaUbi->setId_ubi($faker->numberBetween(1, 1000));
        $oTarifaUbi->setIdTarifaVo(new TarifaId($faker->numberBetween(1, 1000)));
        $oTarifaUbi->setYearVo(new YearNumber($faker->numberBetween(2020, 3000)));
        $oTarifaUbi->setCantidadVo(new TarifaCantidad($faker->randomFloat(2, 0, 100)));
        $oTarifaUbi->setObservVo(new ObservCasaText($faker->word));
        $oTarifaUbi->setIdSerieVo(new SerieId($faker->numberBetween(1, 100)));

        return $oTarifaUbi;
    }

    /**
     * Crea múltiples instancias de TarifaUbi
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

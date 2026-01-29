<?php

namespace Tests\factories\actividadtarifas;

use Faker\Factory;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\value_objects\SfsvId;
use src\ubis\domain\value_objects\ObservCasaText;

/**
 * Factory para crear instancias de TipoTarifa para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoTarifaFactory
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
     * Crea una instancia simple de TipoTarifa con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TipoTarifa
    {
        $id = $id ?? (9900 + random_int(100, 999));
        $oTipoTarifa = new TipoTarifa();
        $oTipoTarifa->setId_tarifa($id);

        $oTipoTarifa->setModoVo(new TarifaModoId(0));
        $oTipoTarifa->setSfsvVo(new SfsvId(1));

        return $oTipoTarifa;
    }

    /**
     * Crea una instancia de TipoTarifa con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TipoTarifa
     */
    public function create(?int $id = null): TipoTarifa
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900 + random_int(100, 999));

        $oTipoTarifa = new TipoTarifa();
        $oTipoTarifa->setId_tarifa($id);

        $oTipoTarifa->setModoVo(new TarifaModoId($faker->randomKey(TarifaModoId::getArrayModo())));
        $oTipoTarifa->setLetraVo(new TarifaLetraCode($faker->lexify('??????')));
        $oTipoTarifa->setSfsvVo(new SfsvId($faker->randomElement([1,2])));
        $oTipoTarifa->setObservVo(new ObservCasaText($faker->text));

        return $oTipoTarifa;
    }

    /**
     * Crea múltiples instancias de TipoTarifa
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

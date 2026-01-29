<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\TelecoUbi;
use src\ubis\domain\value_objects\NumTelecoText;
use src\ubis\domain\value_objects\ObservTelecoText;

/**
 * Factory para crear instancias de TelecoUbi para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TelecoUbiFactory
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
     * Crea una instancia simple de TelecoUbi con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TelecoUbi
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTelecoUbi = new TelecoUbi();
        $oTelecoUbi->setId_item($id);

        $oTelecoUbi->setId_tipo_teleco(3);
        $oTelecoUbi->setNumTelecoVo(new NumTelecoText('test_num_teleco_vo'));
        $oTelecoUbi->setId_ubi(100134);

        return $oTelecoUbi;
    }

    /**
     * Crea una instancia de TelecoUbi con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TelecoUbi
     */
    public function create(?int $id = null): TelecoUbi
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTelecoUbi = new TelecoUbi();
        $oTelecoUbi->setId_item($id);
        $oTelecoUbi->setId_ubi($faker->numberBetween(1001, 3100));

        $oTelecoUbi->setId_tipo_teleco($faker->numberBetween(1, 100));
        $oTelecoUbi->setId_desc_teleco($faker->numberBetween(1, 100));
        $oTelecoUbi->setNumTelecoVo(new NumTelecoText($faker->phoneNumber));
        $oTelecoUbi->setObservVo(new ObservTelecoText($faker->word));

        return $oTelecoUbi;
    }

    /**
     * Crea múltiples instancias de TelecoUbi
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

<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\personas\domain\entity\TelecoPersona;
use src\ubis\domain\value_objects\NumTelecoText;
use src\ubis\domain\value_objects\ObservTelecoText;

/**
 * Factory para crear instancias de TelecoPersona para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TelecoPersonaFactory
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
     * Crea una instancia simple de TelecoPersona con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TelecoPersona
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTelecoPersona = new TelecoPersona();
        $oTelecoPersona->setId_item($id);

        $oTelecoPersona->setId_nom(10011);
        $oTelecoPersona->setId_tipo_teleco(1);
        $oTelecoPersona->setNumTelecoVo(new NumTelecoText('test_num_teleco_vo'));

        return $oTelecoPersona;
    }

    /**
     * Crea una instancia de TelecoPersona con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TelecoPersona
     */
    public function create(?int $id = null): TelecoPersona
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTelecoPersona = new TelecoPersona();
        $oTelecoPersona->setId_item($id);

        $oTelecoPersona->setId_nom($faker->numberBetween(10011, 10000));
        $oTelecoPersona->setId_tipo_teleco($faker->numberBetween(1, 20));
        $oTelecoPersona->setNumTelecoVo(new NumTelecoText($faker->phoneNumber));
        $oTelecoPersona->setObservVo(new ObservTelecoText($faker->word));
        $oTelecoPersona->setId_desc_teleco($faker->phoneNumber);

        return $oTelecoPersona;
    }

    /**
     * Crea múltiples instancias de TelecoPersona
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

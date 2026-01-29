<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\TipoTeleco;
use src\ubis\domain\value_objects\TipoTelecoCode;
use src\ubis\domain\value_objects\TipoTelecoName;

/**
 * Factory para crear instancias de TipoTeleco para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoTelecoFactory
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
     * Crea una instancia simple de TipoTeleco con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TipoTeleco
    {
        $faker = Factory::create('es_ES');

        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTipoTeleco = new TipoTeleco();
        $oTipoTeleco->setId($id);
        $oTipoTeleco->setTipoTelecoVo(TipoTelecoCode::fromNullableString(substr($faker->word, 0, 10)));


        return $oTipoTeleco;
    }

    /**
     * Crea una instancia de TipoTeleco con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TipoTeleco
     */
    public function create(?int $id = null): TipoTeleco
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTipoTeleco = new TipoTeleco();
        $oTipoTeleco->setId($id);

        $oTipoTeleco->setTipoTelecoVo(TipoTelecoCode::fromNullableString(substr($faker->word, 0, 10)));
        $oTipoTeleco->setNombreTelecoVo(new TipoTelecoName($faker->name));
        $oTipoTeleco->setUbi($faker->boolean);
        $oTipoTeleco->setPersona($faker->boolean);

        return $oTipoTeleco;
    }

    /**
     * Crea múltiples instancias de TipoTeleco
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

<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\TipoCasa;
use src\ubis\domain\value_objects\TipoCasaCode;
use src\ubis\domain\value_objects\TipoCasaName;

/**
 * Factory para crear instancias de TipoCasa para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoCasaFactory
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
     * Crea una instancia simple de TipoCasa con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): TipoCasa
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? substr($faker->word, 0, 8);

        $oTipoCasa = new TipoCasa();
        $oTipoCasa->setTipoCasaVo(TipoCasaCode::fromNullableString($id));

        return $oTipoCasa;
    }

    /**
     * Crea una instancia de TipoCasa con datos realistas usando Faker
     * @param string|null $id ID específico o null para generar uno aleatorio
     * @return TipoCasa
     */
    public function create(?string $id = null): TipoCasa
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? substr($faker->word, 0, 8);

        $oTipoCasa = new TipoCasa();
        $oTipoCasa->setTipoCasaVo(TipoCasaCode::fromNullableString($id));

        $oTipoCasa->setNombreTipoCasaVo(new TipoCasaName($faker->name));

        return $oTipoCasa;
    }

    /**
     * Crea múltiples instancias de TipoCasa
     * @param int $count Número de instancias a crear
     * @return array
     */
    public function createMany(int $count): array
    {
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create();
        }

        return $instances;
    }
}

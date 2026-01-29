<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\TipoCentro;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoCentroName;

/**
 * Factory para crear instancias de TipoCentro para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoCentroFactory
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
     * Crea una instancia simple de TipoCentro con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): TipoCentro
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? substr($faker->word, 0, 5);

        $oTipoCentro = new TipoCentro();
        $oTipoCentro->setTipoCtrVo(TipoCentroCode::fromNullableString($id));

        return $oTipoCentro;
    }

    /**
     * Crea una instancia de TipoCentro con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TipoCentro
     */
    public function create(?string $id = null): TipoCentro
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? substr($faker->word, 0, 5);

        $oTipoCentro = new TipoCentro();
        $oTipoCentro->setTipoCtrVo(TipoCentroCode::fromNullableString($id));

        $oTipoCentro->setNombreTipoCtrVo(new TipoCentroName($faker->name));

        return $oTipoCentro;
    }

    /**
     * Crea múltiples instancias de TipoCentro
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

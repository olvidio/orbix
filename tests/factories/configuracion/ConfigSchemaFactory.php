<?php

namespace Tests\factories\configuracion;

use Faker\Factory;
use src\configuracion\domain\entity\ConfigSchema;
use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;

/**
 * Factory para crear instancias de ConfigSchema para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ConfigSchemaFactory
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
     * Crea una instancia simple de ConfigSchema con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): ConfigSchema
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? $faker->word;

        $oConfigSchema = new ConfigSchema();
        $oConfigSchema->setParametroVo($id);

        return $oConfigSchema;
    }

    /**
     * Crea una instancia de ConfigSchema con datos realistas usando Faker
     * @param string|null $id ID específico o null para generar uno aleatorio
     * @return ConfigSchema
     */
    public function create(?string $id = null): ConfigSchema
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? $faker->word;

        $oConfigSchema = new ConfigSchema();
        $oConfigSchema->setParametroVo($id);

        $oConfigSchema->setValorVo(new ConfigValor($faker->word));

        return $oConfigSchema;
    }

    /**
     * Crea múltiples instancias de ConfigSchema
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

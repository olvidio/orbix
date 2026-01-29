<?php

namespace Tests\factories\actividadessacd;

use Faker\Factory;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\actividadessacd\domain\value_objects\SacdTextoClave;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use src\shared\domain\value_objects\LocaleCode;

/**
 * Factory para crear instancias de ActividadSacdTexto para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadSacdTextoFactory
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
     * Crea una instancia simple de ActividadSacdTexto con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadSacdTexto
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadSacdTexto = new ActividadSacdTexto();
        $oActividadSacdTexto->setId_item($id);

        $oActividadSacdTexto->setIdiomaVo(new LocaleCode('ca_ES.UTF-8'));
        $oActividadSacdTexto->setClaveVo(new SacdTextoClave('test_clave_vo'));

        return $oActividadSacdTexto;
    }

    /**
     * Crea una instancia de ActividadSacdTexto con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadSacdTexto
     */
    public function create(?int $id = null): ActividadSacdTexto
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadSacdTexto = new ActividadSacdTexto();
        $oActividadSacdTexto->setId_item($id);

        $oActividadSacdTexto->setIdiomaVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oActividadSacdTexto->setClaveVo(new SacdTextoClave($faker->word));
        $oActividadSacdTexto->setTextoVo(new SacdTextoTexto($faker->word));

        return $oActividadSacdTexto;
    }

    /**
     * Crea múltiples instancias de ActividadSacdTexto
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

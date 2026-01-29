<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoTexto;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\shared\domain\value_objects\LocaleCode;

/**
 * Factory para crear instancias de EncargoTexto para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoTextoFactory
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
     * Crea una instancia simple de EncargoTexto con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoTexto
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoTexto = new EncargoTexto();
        $oEncargoTexto->setId_item($id);

        $oEncargoTexto->setIdiomaVo(new LocaleCode('ca_ES.UTF-8'));
        $oEncargoTexto->setClaveVo(new EncargoTextClave('test_clave_vo'));

        return $oEncargoTexto;
    }

    /**
     * Crea una instancia de EncargoTexto con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoTexto
     */
    public function create(?int $id = null): EncargoTexto
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoTexto = new EncargoTexto();
        $oEncargoTexto->setId_item($id);

        $oEncargoTexto->setIdiomaVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oEncargoTexto->setClaveVo(new EncargoTextClave($faker->word));
        $oEncargoTexto->setTextoVo(new EncargoText($faker->text));

        return $oEncargoTexto;
    }

    /**
     * Crea múltiples instancias de EncargoTexto
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

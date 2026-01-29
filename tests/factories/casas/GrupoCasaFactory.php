<?php

namespace Tests\factories\casas;

use Faker\Factory;
use src\casas\domain\entity\GrupoCasa;

/**
 * Factory para crear instancias de GrupoCasa para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class GrupoCasaFactory
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
     * Crea una instancia simple de GrupoCasa con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null, ?int $idUbiPadre = null, ?int $idUbiHijo = null): GrupoCasa
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oGrupoCasa = new GrupoCasa();
        $oGrupoCasa->setId_item($id);

        $oGrupoCasa->setId_ubi_padre($idUbiPadre ?? -10019001);
        $oGrupoCasa->setId_ubi_hijo($idUbiHijo ?? -10019002);

        return $oGrupoCasa;
    }

    /**
     * Crea una instancia de GrupoCasa con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return GrupoCasa
     */
    public function create(?int $id = null): GrupoCasa
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oGrupoCasa = new GrupoCasa();
        $oGrupoCasa->setId_item($id);

        $oGrupoCasa->setId_ubi_padre($faker->numberBetween(1, 1000));
        $oGrupoCasa->setId_ubi_hijo($faker->numberBetween(1, 1000));

        return $oGrupoCasa;
    }

    /**
     * Crea múltiples instancias de GrupoCasa
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

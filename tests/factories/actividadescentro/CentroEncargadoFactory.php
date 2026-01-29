<?php

namespace Tests\factories\actividadescentro;

use Faker\Factory;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadescentro\domain\value_objects\CentroEncargadoOrden;
use src\actividadescentro\domain\value_objects\CentroEncargadoPk;
use src\actividadescentro\domain\value_objects\CentroEncargadoTexto;

/**
 * Factory para crear instancias de CentroEncargado para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroEncargadoFactory
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
     * Crea una instancia simple de CentroEncargado con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?CentroEncargadoPk $pk = null): CentroEncargado
    {
        $oCentroEncargado = new CentroEncargado();
        if ($pk === null) {
            $oCentroEncargado->setId_activ(3001145);
            $oCentroEncargado->setId_ubi(3001146);
        } else {
            $oCentroEncargado->setId_activ($pk->IdActiv());
            $oCentroEncargado->setId_ubi($pk->IdUbi());
        }

        return $oCentroEncargado;
    }

    /**
     * Crea una instancia de CentroEncargado con datos realistas usando Faker
     * @param CentroEncargadoPk|null $pk ID específico o null para generar uno aleatorio
     * @return CentroEncargado
     */
    public function create(?CentroEncargadoPk $pk = null): CentroEncargado
    {
        $faker = Factory::create('es_ES');

        $oCentroEncargado = new CentroEncargado();
        if ($pk === null) {
            $oCentroEncargado->setId_activ($faker->numberBetween(30011, 300000));
            $oCentroEncargado->setId_ubi($faker->numberBetween(30011, 300000));
        } else {
            $oCentroEncargado->setId_activ($pk->IdActiv());
            $oCentroEncargado->setId_ubi($pk->IdUbi());
        }

        $oCentroEncargado->setNumOrdenVo(new CentroEncargadoOrden($faker->numberBetween(1, 10)));
        $oCentroEncargado->setEncargoVo(new CentroEncargadoTexto($faker->word));

        return $oCentroEncargado;
    }

    /**
     * Crea múltiples instancias de CentroEncargado
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

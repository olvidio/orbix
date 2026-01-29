<?php

namespace Tests\factories\actividadplazas;

use Faker\Factory;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PeticionOrden;
use src\actividadplazas\domain\value_objects\PeticionTipo;
use src\actividadplazas\domain\value_objects\PlazaPeticionPk;

/**
 * Factory para crear instancias de PlazaPeticion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PlazaPeticionFactory
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
     * Crea una instancia simple de PlazaPeticion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?PlazaPeticionPk $pk = null): PlazaPeticion
    {
        $oPlazaPeticion = new PlazaPeticion();
        if ($pk === null) {
            $oPlazaPeticion->setId_activ(10018001);
            $oPlazaPeticion->setId_nom(10019001);
        } else {
            $oPlazaPeticion->setId_activ($pk->idActiv());
            $oPlazaPeticion->setId_nom($pk->idNom());
        }

        $oPlazaPeticion->setOrdenVo(new PeticionOrden(3));

        return $oPlazaPeticion;
    }

    /**
     * Crea una instancia de PlazaPeticion con datos realistas usando Faker
     * @param PlazaPeticionPk|null $pk ID específico o null para generar uno aleatorio
     * @return PlazaPeticion
     */
    public function create(?PlazaPeticionPk $pk = null): PlazaPeticion
    {
        $faker = Factory::create('es_ES');

        $oPlazaPeticion = new PlazaPeticion();
        if ($pk === null) {
            $oPlazaPeticion->setId_activ(10018001);
            $oPlazaPeticion->setId_nom(10019001);
        } else {
            $oPlazaPeticion->setId_activ($pk->idActiv());
            $oPlazaPeticion->setId_nom($pk->idNom());
        }

        $oPlazaPeticion->setOrdenVo(new PeticionOrden($faker->numberBetween(1, 10)));
        $oPlazaPeticion->setTipoVo(new PeticionTipo($faker->text));

        return $oPlazaPeticion;
    }

    /**
     * Crea múltiples instancias de PlazaPeticion
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

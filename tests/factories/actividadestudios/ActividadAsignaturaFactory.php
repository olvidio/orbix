<?php

namespace Tests\factories\actividadestudios;

use Faker\Factory;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;
use src\actividadestudios\domain\value_objects\AvisProfesor;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de ActividadAsignatura para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadAsignaturaFactory
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
     * Crea una instancia simple de ActividadAsignatura con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?ActividadAsignaturaPk $pk = null): ActividadAsignatura
    {
        $oActividadAsignatura = new ActividadAsignatura();
        if ($pk === null) {
            $oActividadAsignatura->setId_activ(10018001);
            $oActividadAsignatura->setId_asignatura(1234);
        } else {
            $oActividadAsignatura->setId_activ($pk->IdActiv());
            $oActividadAsignatura->setId_asignatura($pk->IdAsignatura());
        }

        return $oActividadAsignatura;
    }

    /**
     * Crea una instancia de ActividadAsignatura con datos realistas usando Faker
     * @param ActividadAsignaturaPk|null $pk ID específico o null para generar uno aleatorio
     * @return ActividadAsignatura
     */
    public function create(?ActividadAsignaturaPk $pk = null): ActividadAsignatura
    {
        $faker = Factory::create('es_ES');

        $oActividadAsignatura = new ActividadAsignatura();
        if ($pk === null) {
            $oActividadAsignatura->setId_activ($faker->numberBetween(30011, 300000));
            $oActividadAsignatura->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1234, 9999)));
        } else {
            $oActividadAsignatura->setId_activ($pk->IdActiv());
            $oActividadAsignatura->setId_asignatura($pk->IdAsignatura());
        }

        $oActividadAsignatura->setId_profesor($faker->numberBetween(10011, 100000));
        $oActividadAsignatura->setAvisProfesorVo(new AvisProfesor($faker->randomLetter));
        $oActividadAsignatura->setTipoVo(new TipoActividadAsignatura($faker->randomElement(['p','v','i'])));
        $oActividadAsignatura->setF_ini(new DateTimeLocal($faker->date()));
        $oActividadAsignatura->setF_fin(new DateTimeLocal($faker->date()));

        return $oActividadAsignatura;
    }

    /**
     * Crea múltiples instancias de ActividadAsignatura
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

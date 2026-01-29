<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorDirector;
use src\asignaturas\domain\value_objects\DepartamentoId;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorDirector para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorDirectorFactory
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
     * Crea una instancia simple de ProfesorDirector con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorDirector
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorDirector = new ProfesorDirector();
        $oProfesorDirector->setId_item($id);

        $oProfesorDirector->setId_nom(1);
        $oProfesorDirector->setId_departamento(33);

        return $oProfesorDirector;
    }

    /**
     * Crea una instancia de ProfesorDirector con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorDirector
     */
    public function create(?int $id = null): ProfesorDirector
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorDirector = new ProfesorDirector();
        $oProfesorDirector->setId_item($id);

        $oProfesorDirector->setEscritoNombramientoVo(new EscritoNombramiento($faker->word));
        $oProfesorDirector->setEscritoCeseVo(new EscritoCese($faker->word));
        $oProfesorDirector->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorDirector->setId_departamento($faker->numberBetween(1, 1000));
        $oProfesorDirector->setF_nombramiento(new DateTimeLocal($faker->date()));
        $oProfesorDirector->setF_cese(new DateTimeLocal($faker->date()));

        return $oProfesorDirector;
    }

    /**
     * Crea múltiples instancias de ProfesorDirector
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

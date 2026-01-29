<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorAmpliacion;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorAmpliacion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorAmpliacionFactory
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
     * Crea una instancia simple de ProfesorAmpliacion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorAmpliacion
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorAmpliacion = new ProfesorAmpliacion();
        $oProfesorAmpliacion->setId_item($id);

        $oProfesorAmpliacion->setId_nom(1);
        $oProfesorAmpliacion->setIdAsignaturaVo(new AsignaturaId(1001));

        return $oProfesorAmpliacion;
    }

    /**
     * Crea una instancia de ProfesorAmpliacion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorAmpliacion
     */
    public function create(?int $id = null): ProfesorAmpliacion
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorAmpliacion = new ProfesorAmpliacion();
        $oProfesorAmpliacion->setId_item($id);

        $oProfesorAmpliacion->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorAmpliacion->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween( 1000,5000)));
        $oProfesorAmpliacion->setEscritoNombramientoVo(new EscritoNombramiento($faker->word));
        $oProfesorAmpliacion->setEscritoCeseVo(new EscritoCese($faker->word));
        $oProfesorAmpliacion->setF_nombramiento(new DateTimeLocal($faker->date()));
        $oProfesorAmpliacion->setF_cese(new DateTimeLocal($faker->date()));

        return $oProfesorAmpliacion;
    }

    /**
     * Crea múltiples instancias de ProfesorAmpliacion
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

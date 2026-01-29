<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorStgr;
use src\asignaturas\domain\value_objects\DepartamentoId;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\ProfesorTipoId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorStgr para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorStgrFactory
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
     * Crea una instancia simple de ProfesorStgr con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorStgr
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorStgr = new ProfesorStgr();
        $oProfesorStgr->setId_item($id);

        $oProfesorStgr->setId_nom(1);
        $oProfesorStgr->setId_departamento(34);

        return $oProfesorStgr;
    }

    /**
     * Crea una instancia de ProfesorStgr con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorStgr
     */
    public function create(?int $id = null): ProfesorStgr
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorStgr = new ProfesorStgr();
        $oProfesorStgr->setId_item($id);

        $oProfesorStgr->setIdDepartamentoVo(new DepartamentoId($faker->numberBetween(1, 10)));
        $oProfesorStgr->setEscritoNombramientoVo(new EscritoNombramiento($faker->word));
        $oProfesorStgr->setIdTipoProfesorVo(new ProfesorTipoId($faker->numberBetween(1, 10)));
        $oProfesorStgr->setEscritoCeseVo(new EscritoCese($faker->word));
        $oProfesorStgr->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorStgr->setF_nombramiento(new DateTimeLocal($faker->date()));
        $oProfesorStgr->setF_cese(new DateTimeLocal($faker->date()));

        return $oProfesorStgr;
    }

    /**
     * Crea múltiples instancias de ProfesorStgr
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

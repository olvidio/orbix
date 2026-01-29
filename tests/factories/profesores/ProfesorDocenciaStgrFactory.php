<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\procesos\domain\value_objects\ActividadId;
use src\profesores\domain\value_objects\Acta;
use src\profesores\domain\value_objects\CursoInicio;
use src\profesores\domain\value_objects\ProfesorTipoName;

/**
 * Factory para crear instancias de ProfesorDocenciaStgr para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorDocenciaStgrFactory
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
     * Crea una instancia simple de ProfesorDocenciaStgr con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorDocenciaStgr
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorDocenciaStgr = new ProfesorDocenciaStgr();
        $oProfesorDocenciaStgr->setId_item($id);

        $oProfesorDocenciaStgr->setId_nom(1);
        $oProfesorDocenciaStgr->setId_asignatura(1001);
        $oProfesorDocenciaStgr->setCurso_inicio(1);

        return $oProfesorDocenciaStgr;
    }

    /**
     * Crea una instancia de ProfesorDocenciaStgr con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorDocenciaStgr
     */
    public function create(?int $id = null): ProfesorDocenciaStgr
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorDocenciaStgr = new ProfesorDocenciaStgr();
        $oProfesorDocenciaStgr->setId_item($id);

        $oProfesorDocenciaStgr->setIdActivVo(new ActividadId($faker->word));
        $oProfesorDocenciaStgr->setTipoVo(new TipoActividadAsignatura($faker->word));
        $oProfesorDocenciaStgr->setActaVo(new ActaNumero($faker->word));
        $oProfesorDocenciaStgr->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorDocenciaStgr->setId_asignatura($faker->numberBetween( 1000, 5000));
        $oProfesorDocenciaStgr->setCurso_inicio($faker->numberBetween(1, 1000));

        return $oProfesorDocenciaStgr;
    }

    /**
     * Crea múltiples instancias de ProfesorDocenciaStgr
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

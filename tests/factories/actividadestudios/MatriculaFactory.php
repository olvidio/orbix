<?php

namespace Tests\factories\actividadestudios;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\domain\entity\Matricula;
use src\actividadestudios\domain\value_objects\ActividadMatriculaPk;
use src\actividadestudios\domain\value_objects\NotaMax;
use src\actividadestudios\domain\value_objects\NotaNum;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Factory para crear instancias de Matricula para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class MatriculaFactory
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
     * Crea una instancia simple de Matricula con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?ActividadMatriculaPk $pk = null): Matricula
    {
        $oMatricula = new Matricula();

        if ($pk === null) {
            $oMatricula->setId_activ(30011345);
            $oMatricula->setId_nom(10011);
            $oMatricula->setId_asignatura(1234);
        } else {
            $oMatricula->setId_activ($pk->idActiv());
            $oMatricula->setId_nom($pk->idNom());
            $oMatricula->setId_asignatura($pk->idAsignatura());
        }

        $oMatricula->setIdSituacionVo(new NotaSituacion(10));

        return $oMatricula;
    }

    /**
     * Crea una instancia de Matricula con datos realistas usando Faker
     * @param ActividadMatriculaPk|null $pk ID específico o null para generar uno aleatorio
     * @return Matricula
     */
    public function create(?ActividadMatriculaPk $pk = null): Matricula
    {
        $faker = Factory::create('es_ES');

        $oMatricula = new Matricula();
        if ($pk === null) {
            $oMatricula->setId_activ(30011345);
            $oMatricula->setId_nom($faker->numberBetween(10011, 100000));
            $oMatricula->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1234, 9999)));
        } else {
            $oMatricula->setId_activ($pk->idActiv());
            $oMatricula->setId_nom($pk->idNom());
            $oMatricula->setId_asignatura($pk->idAsignatura());
        }

        $oMatricula->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1234, 9999)));
        $oMatricula->setId_nom($faker->numberBetween(10011, 100000));
        $oMatricula->setIdSituacionVo(new NotaSituacion($faker->randomKey(NotaSituacion::getArraySituacionTxt())));
        $oMatricula->setPreceptor($faker->boolean);
        $oMatricula->setIdNivelVo(new NivelStgrId($faker->randomKey(NivelStgrId::getArrayNivelStgr())));
        $oMatricula->setNotaNumVo(new NotaNum($faker->randomFloat(2, 0, 100)));
        $oMatricula->setNotaMaxVo(new NotaMax($faker->numberBetween(1, 10)));
        $oMatricula->setId_preceptor($faker->numberBetween(10011, 100000));
        $oMatricula->setActaVo(new ActaNumero(substr($faker->word, 0, 6) . " " . $faker->numberBetween(1, 100) . "/" . $faker->numberBetween(20, 30)));

        return $oMatricula;
    }

    /**
     * Crea múltiples instancias de Matricula
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

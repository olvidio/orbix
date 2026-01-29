<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorTituloEst;
use src\profesores\domain\value_objects\CentroDntName;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\TituloName;
use src\profesores\domain\value_objects\YearNumber;

/**
 * Factory para crear instancias de ProfesorTituloEst para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorTituloEstFactory
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
     * Crea una instancia simple de ProfesorTituloEst con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorTituloEst
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorTituloEst = new ProfesorTituloEst();
        $oProfesorTituloEst->setId_item($id);

        $oProfesorTituloEst->setId_nom(1);
        $oProfesorTituloEst->setTituloVo(new PublicacionTitulo('Uno de test'));

        return $oProfesorTituloEst;
    }

    /**
     * Crea una instancia de ProfesorTituloEst con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorTituloEst
     */
    public function create(?int $id = null): ProfesorTituloEst
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorTituloEst = new ProfesorTituloEst();
        $oProfesorTituloEst->setId_item($id);

        $oProfesorTituloEst->setTituloVo(new PublicacionTitulo(substr($faker->word, 0, 25)));
        $oProfesorTituloEst->setCentroDntVo(new CentroDntName($faker->word));
        $oProfesorTituloEst->setYearVo(new YearNumber($faker->numberBetween(1, 10)));
        $oProfesorTituloEst->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorTituloEst->setEclesiastico($faker->boolean);

        return $oProfesorTituloEst;
    }

    /**
     * Crea múltiples instancias de ProfesorTituloEst
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

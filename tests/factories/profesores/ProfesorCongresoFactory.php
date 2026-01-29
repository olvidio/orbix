<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorCongreso;
use src\profesores\domain\value_objects\CongresoName;
use src\profesores\domain\value_objects\CongresoTipo;
use src\profesores\domain\value_objects\LugarName;
use src\profesores\domain\value_objects\OrganizaName;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorCongreso para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorCongresoFactory
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
     * Crea una instancia simple de ProfesorCongreso con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorCongreso
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorCongreso = new ProfesorCongreso();
        $oProfesorCongreso->setId_item($id);

        $oProfesorCongreso->setId_nom(1);
        $oProfesorCongreso->setCongresoVo(new CongresoName('Uno de test'));

        return $oProfesorCongreso;
    }

    /**
     * Crea una instancia de ProfesorCongreso con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorCongreso
     */
    public function create(?int $id = null): ProfesorCongreso
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorCongreso = new ProfesorCongreso();
        $oProfesorCongreso->setId_item($id);

        $oProfesorCongreso->setCongresoVo(new CongresoName($faker->word));
        $oProfesorCongreso->setLugarVo(new LugarName($faker->word));
        $oProfesorCongreso->setOrganizaVo(new OrganizaName($faker->word));
        $oProfesorCongreso->setTipoVo(new CongresoTipo($faker->numberBetween(1, 10)));
        $oProfesorCongreso->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorCongreso->setF_ini(new DateTimeLocal($faker->date()));
        $oProfesorCongreso->setF_fin(new DateTimeLocal($faker->date()));

        return $oProfesorCongreso;
    }

    /**
     * Crea múltiples instancias de ProfesorCongreso
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

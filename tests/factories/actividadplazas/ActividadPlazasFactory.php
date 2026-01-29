<?php

namespace Tests\factories\actividadplazas;

use Faker\Factory;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use src\actividadplazas\domain\value_objects\PlazaClCode;
use src\actividadplazas\domain\value_objects\PlazasNumero;

/**
 * Factory para crear instancias de ActividadPlazas para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadPlazasFactory
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
     * Crea una instancia simple de ActividadPlazas con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadPlazas
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadPlazas = new ActividadPlazas();
        $oActividadPlazas->setId_activ($id);

        $oActividadPlazas->setId_dl(1);
        $oActividadPlazas->setDlTablaVo(new DelegacionTablaCode('test_dl'));

        return $oActividadPlazas;
    }

    /**
     * Crea una instancia de ActividadPlazas con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadPlazas
     */
    public function create(?int $id = null): ActividadPlazas
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActividadPlazas = new ActividadPlazas();
        $oActividadPlazas->setId_activ($id);

        $oActividadPlazas->setId_dl($faker->numberBetween(1, 1000));
        $oActividadPlazas->setPlazasVo(new PlazasNumero($faker->numberBetween(1, 10)));
        $oActividadPlazas->setClVo(new PlazaClCode($faker->word));
        $oActividadPlazas->setDlTablaVo(new DelegacionTablaCode(substr($faker->word, 0, 8)));
        $oActividadPlazas->setCedidas(json_encode(['dlb' => 33, 'dlz' => 45]));

        return $oActividadPlazas;
    }

    /**
     * Crea múltiples instancias de ActividadPlazas
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

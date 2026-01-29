<?php

namespace Tests\factories\procesos;

use Faker\Factory;
use src\actividades\domain\value_objects\ActividadTipoIdTxt;
use src\procesos\domain\entity\PermUsuarioActividad;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\procesos\domain\value_objects\FaseId;

/**
 * Factory para crear instancias de PermUsuarioActividad para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PermUsuarioActividadFactory
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
     * Crea una instancia simple de PermUsuarioActividad con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PermUsuarioActividad
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPermUsuarioActividad = new PermUsuarioActividad();
        $oPermUsuarioActividad->setId_item($id);

        $oPermUsuarioActividad->setDl_propia(false);
        $oPermUsuarioActividad->setIdTipoActivTxtVo(new ActividadTipoIdTxt(123456));

        return $oPermUsuarioActividad;
    }

    /**
     * Crea una instancia de PermUsuarioActividad con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PermUsuarioActividad
     */
    public function create(?int $id = null): PermUsuarioActividad
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPermUsuarioActividad = new PermUsuarioActividad();
        $oPermUsuarioActividad->setId_item($id);

        $oPermUsuarioActividad->setId_usuario($faker->numberBetween(1, 1000));
        $oPermUsuarioActividad->setDl_propia($faker->boolean);
        $oPermUsuarioActividad->setId_tipo_activ_txt($faker->word);
        $oPermUsuarioActividad->setIdTipoActivTxtVo(new ActividadTipoIdTxt($faker->numberBetween(100000, 299999)));
        $oPermUsuarioActividad->setFaseRefVo(new FaseId($faker->numberBetween(1, 10)));
        $oPermUsuarioActividad->setAfecta_a($faker->numberBetween(1, 1000));
        $oPermUsuarioActividad->setPerm_on($faker->numberBetween(1, 1000));
        $oPermUsuarioActividad->setPerm_off($faker->numberBetween(1, 1000));

        return $oPermUsuarioActividad;
    }

    /**
     * Crea múltiples instancias de PermUsuarioActividad
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

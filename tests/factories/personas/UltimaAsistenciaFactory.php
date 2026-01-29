<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\personas\domain\entity\UltimaAsistencia;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\personas\domain\value_objects\AsistenciaDescripcionText;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de UltimaAsistencia para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class UltimaAsistenciaFactory
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
     * Crea una instancia simple de UltimaAsistencia con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): UltimaAsistencia
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oUltimaAsistencia = new UltimaAsistencia();
        $oUltimaAsistencia->setId_item($id);

        $oUltimaAsistencia->setId_nom(1001);

        return $oUltimaAsistencia;
    }

    /**
     * Crea una instancia de UltimaAsistencia con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return UltimaAsistencia
     */
    public function create(?int $id = null): UltimaAsistencia
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oUltimaAsistencia = new UltimaAsistencia();
        $oUltimaAsistencia->setId_item($id);

        $oUltimaAsistencia->setId_nom($faker->numberBetween(1001, 10000));
        $oUltimaAsistencia->setId_tipo_activ($faker->numberBetween(1001, 20000));
        $oUltimaAsistencia->setIdTipoActivVo(new ActividadTipoId($faker->numerify('######')));
        $oUltimaAsistencia->setF_ini(new DateTimeLocal($faker->date()));
        $oUltimaAsistencia->setDescripcionVo(new AsistenciaDescripcionText($faker->sentence));
        $oUltimaAsistencia->setCdr($faker->boolean);

        return $oUltimaAsistencia;
    }

    /**
     * Crea múltiples instancias de UltimaAsistencia
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

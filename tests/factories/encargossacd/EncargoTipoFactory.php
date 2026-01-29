<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\EncargoTipoText;
use src\encargossacd\domain\value_objects\EncargoModHorarioId;

/**
 * Factory para crear instancias de EncargoTipo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoTipoFactory
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
     * Crea una instancia simple de EncargoTipo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoTipo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoTipo = new EncargoTipo();
        $oEncargoTipo->setId_tipo_enc($id);

        $oEncargoTipo->setTipoEncVo(new EncargoTipoText('test_tipo_enc_vo'));
        $oEncargoTipo->setModHorarioVo(new EncargoModHorarioId(1));

        return $oEncargoTipo;
    }

    /**
     * Crea una instancia de EncargoTipo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoTipo
     */
    public function create(?int $id = null): EncargoTipo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoTipo = new EncargoTipo();
        $oEncargoTipo->setId_tipo_enc($id);

        $oEncargoTipo->setTipoEncVo(new EncargoTipoText($faker->text));
        $oEncargoTipo->setModHorarioVo(new EncargoModHorarioId($faker->numberBetween(1, 3)));

        return $oEncargoTipo;
    }

    /**
     * Crea múltiples instancias de EncargoTipo
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

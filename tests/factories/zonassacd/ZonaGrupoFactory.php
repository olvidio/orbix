<?php

namespace Tests\factories\zonassacd;

use Faker\Factory;
use src\zonassacd\domain\entity\ZonaGrupo;
use src\zonassacd\domain\value_objects\NombreGrupoZona;

/**
 * Factory para crear instancias de ZonaGrupo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ZonaGrupoFactory
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
     * Crea una instancia simple de ZonaGrupo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ZonaGrupo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oZonaGrupo = new ZonaGrupo();
        $oZonaGrupo->setId_grupo($id);


        return $oZonaGrupo;
    }

    /**
     * Crea una instancia de ZonaGrupo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ZonaGrupo
     */
    public function create(?int $id = null): ZonaGrupo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oZonaGrupo = new ZonaGrupo();
        $oZonaGrupo->setId_grupo($id);

        $oZonaGrupo->setNombreGrupoVo(new NombreGrupoZona($faker->name));
        $oZonaGrupo->setNombre_grupo($faker->name);
        $oZonaGrupo->setOrden($faker->numberBetween(1, 100));

        return $oZonaGrupo;
    }

    /**
     * Crea múltiples instancias de ZonaGrupo
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

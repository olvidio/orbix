<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\CentroEllas;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;

/**
 * Factory para crear instancias de CentroEllas para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroEllasFactory
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
     * Crea una instancia simple de CentroEllas con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CentroEllas
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCentroEllas = new CentroEllas();
        $oCentroEllas->setId_ubi($id);

        $oCentroEllas->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCentroEllas->setActive(false);

        return $oCentroEllas;
    }

    /**
     * Crea una instancia de CentroEllas con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CentroEllas
     */
    public function create(?int $id = null): CentroEllas
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCentroEllas = new CentroEllas();
        $oCentroEllas->setId_ubi($id);

        $oCentroEllas->setTipo_ubi($faker->word);
        $oCentroEllas->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCentroEllas->setDlVo(new DelegacionCode($faker->word));
        $oCentroEllas->setPaisVo(new PaisName($faker->word));
        $oCentroEllas->setRegionVo(new RegionNameText($faker->word));
        $oCentroEllas->setActive($faker->boolean);
        $oCentroEllas->setF_active(new DateTimeLocal($faker->date()));
        $oCentroEllas->setSv($faker->boolean);
        $oCentroEllas->setSf($faker->boolean);
        $oCentroEllas->setTipoCtrVo(new TipoCentroCode($faker->word));
        $oCentroEllas->setTipoLaborVo(new TipoLaborId($faker->numberBetween(1, 10)));
        $oCentroEllas->setCdc($faker->boolean);
        $oCentroEllas->setIdCtrPadreVo(new CentroId($faker->numberBetween(1, 10)));
        $oCentroEllas->setId_zona($faker->numberBetween(1, 1000));

        return $oCentroEllas;
    }

    /**
     * Crea múltiples instancias de CentroEllas
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

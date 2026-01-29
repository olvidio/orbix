<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\Centro;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\UbiNombreText;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;

/**
 * Factory para crear instancias de Centro para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroFactory
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
     * Crea una instancia simple de Centro con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Centro
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCentro = new Centro();
        $oCentro->setId_ubi($id);

        $oCentro->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCentro->setActive(false);
        $oCentro->setIdAuto(1);

        return $oCentro;
    }

    /**
     * Crea una instancia de Centro con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Centro
     */
    public function create(?int $id = null): Centro
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCentro = new Centro();
        $oCentro->setId_ubi($id);

        $oCentro->setTipo_ubi($faker->word);
        $oCentro->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCentro->setDlVo(new DelegacionCode($faker->word));
        $oCentro->setPaisVo(new PaisName($faker->word));
        $oCentro->setRegionVo(new RegionNameText($faker->word));
        $oCentro->setActive($faker->boolean);
        $oCentro->setF_active(new DateTimeLocal($faker->date()));
        $oCentro->setSv($faker->boolean);
        $oCentro->setSf($faker->boolean);
        $oCentro->setTipoCtrVo(new TipoCentroCode($faker->word));
        $oCentro->setTipoLaborVo(new TipoLaborId($faker->numberBetween(1, 10)));
        $oCentro->setCdc($faker->boolean);
        $oCentro->setIdCtrPadreVo(new CentroId($faker->numberBetween(1, 10)));
        $oCentro->setIdAuto($faker->numberBetween(1, 1000));

        return $oCentro;
    }

    /**
     * Crea múltiples instancias de Centro
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

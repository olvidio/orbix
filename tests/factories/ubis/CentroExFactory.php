<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\CentroEx;
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
 * Factory para crear instancias de CentroEx para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroExFactory
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
     * Crea una instancia simple de CentroEx con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CentroEx
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCentroEx = new CentroEx();
        $oCentroEx->setId_ubi($id);

        $oCentroEx->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCentroEx->setActive(false);
        $oCentroEx->setId_auto(1);

        return $oCentroEx;
    }

    /**
     * Crea una instancia de CentroEx con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CentroEx
     */
    public function create(?int $id = null): CentroEx
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCentroEx = new CentroEx();
        $oCentroEx->setId_ubi($id);

        $oCentroEx->setTipo_ubi($faker->word);
        $oCentroEx->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCentroEx->setDlVo(new DelegacionCode($faker->word));
        $oCentroEx->setPaisVo(new PaisName($faker->word));
        $oCentroEx->setRegionVo(new RegionNameText($faker->word));
        $oCentroEx->setActive($faker->boolean);
        $oCentroEx->setF_active(new DateTimeLocal($faker->date()));
        $oCentroEx->setSv($faker->boolean);
        $oCentroEx->setSf($faker->boolean);
        $oCentroEx->setTipoCtrVo(new TipoCentroCode($faker->word));
        $oCentroEx->setTipoLaborVo(new TipoLaborId($faker->numberBetween(1, 10)));
        $oCentroEx->setCdc($faker->boolean);
        $oCentroEx->setIdCtrPadreVo(new CentroId($faker->numberBetween(1, 10)));
        $oCentroEx->setId_auto($faker->numberBetween(1, 1000));

        return $oCentroEx;
    }

    /**
     * Crea múltiples instancias de CentroEx
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

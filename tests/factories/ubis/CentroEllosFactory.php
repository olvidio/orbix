<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\CentroEllos;
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
 * Factory para crear instancias de CentroEllos para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroEllosFactory
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
     * Crea una instancia simple de CentroEllos con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CentroEllos
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCentroEllos = new CentroEllos();
        $oCentroEllos->setId_ubi($id);

        $oCentroEllos->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCentroEllos->setActive(false);

        return $oCentroEllos;
    }

    /**
     * Crea una instancia de CentroEllos con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CentroEllos
     */
    public function create(?int $id = null): CentroEllos
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCentroEllos = new CentroEllos();
        $oCentroEllos->setId_ubi($id);

        $oCentroEllos->setTipo_ubi($faker->word);
        $oCentroEllos->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCentroEllos->setDlVo(new DelegacionCode($faker->word));
        $oCentroEllos->setPaisVo(new PaisName($faker->word));
        $oCentroEllos->setRegionVo(new RegionNameText($faker->word));
        $oCentroEllos->setActive($faker->boolean);
        $oCentroEllos->setF_active(new DateTimeLocal($faker->date()));
        $oCentroEllos->setSv($faker->boolean);
        $oCentroEllos->setSf($faker->boolean);
        $oCentroEllos->setTipoCtrVo(new TipoCentroCode($faker->word));
        $oCentroEllos->setTipoLaborVo(new TipoLaborId($faker->numberBetween(1, 10)));
        $oCentroEllos->setCdc($faker->boolean);
        $oCentroEllos->setIdCtrPadreVo(new CentroId($faker->numberBetween(1, 10)));
        $oCentroEllos->setId_zona($faker->numberBetween(1, 1000));

        return $oCentroEllos;
    }

    /**
     * Crea múltiples instancias de CentroEllos
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

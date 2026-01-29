<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\CentroDl;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\UbiNombreText;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\NBuzon;
use src\ubis\domain\value_objects\NumPi;
use src\ubis\domain\value_objects\NumCartas;
use src\ubis\domain\value_objects\ObservCentroText;
use src\ubis\domain\value_objects\NumHabitIndiv;
use src\ubis\domain\value_objects\Plazas;
use src\ubis\domain\value_objects\ZonaId;

/**
 * Factory para crear instancias de CentroDl para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CentroDlFactory
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
     * Crea una instancia simple de CentroDl con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CentroDl
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCentroDl = new CentroDl();
        $oCentroDl->setId_ubi($id);

        $oCentroDl->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCentroDl->setActive(false);
        $oCentroDl->setId_auto(1);

        return $oCentroDl;
    }

    /**
     * Crea una instancia de CentroDl con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CentroDl
     */
    public function create(?int $id = null): CentroDl
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCentroDl = new CentroDl();
        $oCentroDl->setId_ubi($id);

        $oCentroDl->setTipo_ubi($faker->word);
        $oCentroDl->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCentroDl->setDlVo(new DelegacionCode($faker->word));
        $oCentroDl->setPaisVo(new PaisName($faker->word));
        $oCentroDl->setRegionVo(new RegionNameText($faker->word));
        $oCentroDl->setActive($faker->boolean);
        $oCentroDl->setF_active(new DateTimeLocal($faker->date()));
        $oCentroDl->setSv($faker->boolean);
        $oCentroDl->setSf($faker->boolean);
        $oCentroDl->setTipoCtrVo(new TipoCentroCode($faker->word));
        $oCentroDl->setTipoLaborVo(new TipoLaborId($faker->numberBetween(1, 10)));
        $oCentroDl->setCdc($faker->boolean);
        $oCentroDl->setIdCtrPadreVo(new CentroId($faker->numberBetween(1, 10)));
        $oCentroDl->setId_auto($faker->numberBetween(1, 1000));
        $oCentroDl->setNBuzonVo(new NBuzon($faker->numberBetween(1, 10)));
        $oCentroDl->setNumPiVo(new NumPi($faker->numberBetween(1, 10)));
        $oCentroDl->setNumCartasVo(new NumCartas($faker->numberBetween(1, 10)));
        $oCentroDl->setObservVo(new ObservCentroText($faker->word));
        $oCentroDl->setNumHabitIndivVo(new NumHabitIndiv($faker->numberBetween(1, 10)));
        $oCentroDl->setPlazasVo(new Plazas($faker->numberBetween(1, 10)));
        $oCentroDl->setIdZonaVo(new ZonaId($faker->numberBetween(1, 10)));
        $oCentroDl->setSede($faker->boolean);
        $oCentroDl->setNumCartasMensualesVo(new NumCartas($faker->numberBetween(1, 10)));

        return $oCentroDl;
    }

    /**
     * Crea múltiples instancias de CentroDl
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

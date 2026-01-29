<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\Casa;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\value_objects\UbiNombreText;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCasaText;
use src\ubis\domain\value_objects\Plazas;
use src\ubis\domain\value_objects\PlazasMin;
use src\ubis\domain\value_objects\NumSacerdotes;
use src\ubis\domain\value_objects\BibliotecaText;
use src\ubis\domain\value_objects\ObservCasaText;

/**
 * Factory para crear instancias de Casa para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CasaFactory
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
     * Crea una instancia simple de Casa con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Casa
    {
        //que empiece por 1, 2, 3 o -3 y tenga al menos 5 dígitos
        $prefijos = [10000, 20000, 30000, -30000];
        $id = $id ?? ($prefijos[rand(0, 3)] + rand(0, 999));
        $oCasa = new Casa();
        $oCasa->setId_ubi($id);

        $oCasa->setNombreUbiVo(new UbiNombreText('test_nombre_ubi_vo'));
        $oCasa->setActive(false);
        $oCasa->setId_auto(1);

        return $oCasa;
    }

    /**
     * Crea una instancia de Casa con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Casa
     */
    public function create(?int $id = null): Casa
    {
        $faker = Factory::create('es_ES');
        //que empiece por 1, 2, 3 o -3 y tenga al menos 5 dígitos
        $prefijos = [10000, 20000, 30000, -30000];
        $id = $id ?? ($prefijos[rand(0, 3)] + rand(0, 999));

        $oCasa = new Casa();
        $oCasa->setId_ubi($id);

        $oCasa->setNombreUbiVo(new UbiNombreText($faker->name));
        $oCasa->setDlVo(new DelegacionCode($faker->word));
        $oCasa->setPaisVo(new PaisName($faker->word));
        $oCasa->setRegionVo(new RegionNameText($faker->word));
        $oCasa->setActive($faker->boolean);
        $oCasa->setF_active(new DateTimeLocal($faker->date()));
        $oCasa->setSv($faker->boolean);
        $oCasa->setSf($faker->boolean);
        $oCasa->setTipoCasaVo(new TipoCasaText($faker->word));
        $oCasa->setPlazasVo(new Plazas($faker->numberBetween(1, 10)));
        $oCasa->setPlazasMinVo(new PlazasMin($faker->numberBetween(1, 10)));
        $oCasa->setNumSacdVo(new NumSacerdotes($faker->numberBetween(1, 10)));
        $oCasa->setBibliotecaVo(new BibliotecaText($faker->word));
        $oCasa->setObservVo(new ObservCasaText($faker->word));
        $oCasa->setId_auto($faker->numberBetween(1, 1000));

        return $oCasa;
    }

    /**
     * Crea múltiples instancias de Casa
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

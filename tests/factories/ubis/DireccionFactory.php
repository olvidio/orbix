<?php

namespace Tests\factories\ubis;

use Faker\Factory;
use src\ubis\domain\entity\Direccion;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\DireccionId;
use src\ubis\domain\value_objects\DireccionText;
use src\ubis\domain\value_objects\CodigoPostalText;
use src\ubis\domain\value_objects\PoblacionText;
use src\ubis\domain\value_objects\ProvinciaText;
use src\ubis\domain\value_objects\APText;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\ObservDireccionText;
use src\ubis\domain\value_objects\LatitudDecimal;
use src\ubis\domain\value_objects\LongitudDecimal;
use src\ubis\domain\value_objects\PlanoDocText;
use src\ubis\domain\value_objects\PlanoExtensionText;
use src\ubis\domain\value_objects\PlanoNameText;
use src\ubis\domain\value_objects\SedeNameText;

/**
 * Factory para crear instancias de Direccion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class DireccionFactory
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
     * Crea una instancia simple de Direccion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Direccion
    {
        //que empiece por 1, 2, -1 o -2 y tenga al menos 5 dígitos
        $prefijos = [10000, 20000, -10000, -20000];
        $id = $id ?? (int)($prefijos[rand(0, 3)] . rand(0, 999));

        $oDireccion = new Direccion();
        $oDireccion->setId_direccion($id);

        $oDireccion->setPoblacionVo(new PoblacionText('test_poblacion_vo'));

        return $oDireccion;
    }

    /**
     * Crea una instancia de Direccion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Direccion
     */
    public function create(?int $id = null): Direccion
    {
        $faker = Factory::create('es_ES');
        //que empiece por 1, 2, -1 o -2 y tenga al menos 5 dígitos
        $prefijos = [10000, 20000, -10000, -20000];
        $id = $id ?? (int)($prefijos[rand(0, 3)] . rand(0, 999));

        $oDireccion = new Direccion();
        $oDireccion->setId_direccion($id);

        $oDireccion->setDireccionVo(new DireccionText($faker->address));
        $oDireccion->setCodigoPostalVo(new CodigoPostalText($faker->numerify("CODE###")));
        $oDireccion->setPoblacionVo(new PoblacionText($faker->word));
        $oDireccion->setProvinciaVo(new ProvinciaText($faker->word));
        $oDireccion->setAPVo(new APText($faker->word));
        $oDireccion->setPaisVo(new PaisName($faker->word));
        $oDireccion->setF_direccion(new DateTimeLocal($faker->date()));
        $oDireccion->setObservVo(new ObservDireccionText($faker->word));
        $oDireccion->setCpDcha($faker->boolean);
        $oDireccion->setLatitudVo(new LatitudDecimal($faker->randomFloat(2, 0, 100)));
        $oDireccion->setLongitudVo(new LongitudDecimal($faker->randomFloat(2, 0, 100)));
        $oDireccion->setPlanoDocVo(new PlanoDocText($faker->word));
        $oDireccion->setPlanoExtensionVo(new PlanoExtensionText($faker->word));
        $oDireccion->setPlanoNomVo(new PlanoNameText($faker->word));
        $oDireccion->setNomSedeVo(new SedeNameText($faker->word));

        return $oDireccion;
    }

    /**
     * Crea múltiples instancias de Direccion
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        //que empiece por 1, 2, -1 o -2 y tenga al menos 5 dígitos
        $prefijos = [10000, 20000, -10000, -20000];
        $startId = $startId ?? (int)($prefijos[rand(0, 3)] . rand(0, 999));
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}

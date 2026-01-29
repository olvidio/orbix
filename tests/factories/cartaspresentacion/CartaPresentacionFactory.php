<?php

namespace Tests\factories\cartaspresentacion;

use Faker\Factory;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\cartaspresentacion\domain\value_objects\PresEmailText;
use src\cartaspresentacion\domain\value_objects\PresentacionPk;
use src\cartaspresentacion\domain\value_objects\PresNombreText;
use src\cartaspresentacion\domain\value_objects\PresObservText;
use src\cartaspresentacion\domain\value_objects\PresTelefonoText;
use src\cartaspresentacion\domain\value_objects\PresZonaText;

/**
 * Factory para crear instancias de CartaPresentacion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CartaPresentacionFactory
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
     * Crea una instancia simple de CartaPresentacion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?PresentacionPk $pk = null): CartaPresentacion
    {
        $oCartaPresentacion = new CartaPresentacion();
        if ($pk === null) {
            // direccion existente para foreign keys
            $oCartaPresentacion->setId_direccion(-10018001);
            // ubi existente para foreign keys
            $oCartaPresentacion->setId_ubi(-10019001);
        } else {
            $oCartaPresentacion->setId_direccion($pk->idDireccion());
            $oCartaPresentacion->setId_ubi($pk->idUbi());
        }

        return $oCartaPresentacion;
    }

    /**
     * Crea una instancia de CartaPresentacion con datos realistas usando Faker
     * @param PresentacionPk|null $pk ID específico o null para generar uno aleatorio
     * @return CartaPresentacion
     */
    public function create(?PresentacionPk $pk = null): CartaPresentacion
    {
        $faker = Factory::create('es_ES');

        $oCartaPresentacion = new CartaPresentacion();
        if ($pk === null) {
            // direccion existente para foreign keys
            $oCartaPresentacion->setId_direccion(-10018001);
            // ubi existente para foreign keys
            $oCartaPresentacion->setId_ubi(-10019001);
        } else {
            $oCartaPresentacion->setId_direccion($pk->idDireccion());
            $oCartaPresentacion->setId_ubi($pk->idUbi());
        }

        $oCartaPresentacion->setPresNomVo(new PresNombreText($faker->word));
        $oCartaPresentacion->setPresTelfVo(new PresTelefonoText($faker->word));
        $oCartaPresentacion->setPresMailVo(new PresEmailText($faker->word));
        $oCartaPresentacion->setZonaVo(new PresZonaText($faker->word));
        $oCartaPresentacion->setObservVo(new PresObservText($faker->word));

        return $oCartaPresentacion;
    }

    /**
     * Crea múltiples instancias de CartaPresentacion
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

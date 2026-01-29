<?php

namespace Tests\factories\configuracion;

use Faker\Factory;
use src\configuracion\domain\entity\App;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;

/**
 * Factory para crear instancias de App para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class AppFactory
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
     * Crea una instancia simple de App con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): App
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oApp = new App();
        $oApp->setId_app($id);

        $oApp->setNomVo(new AppName('testNombreApp'));

        return $oApp;
    }

    /**
     * Crea una instancia de App con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return App
     */
    public function create(?int $id = null): App
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oApp = new App();
        $oApp->setId_app($id);

        $oApp->setNomVo(new AppName($faker->name));

        return $oApp;
    }

    /**
     * Crea múltiples instancias de App
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

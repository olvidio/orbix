<?php

namespace Tests\factories\cambios;

use Faker\Factory;
use src\cambios\domain\entity\CambioUsuario;
use src\cambios\domain\value_objects\AvisoTipoId;

/**
 * Factory para crear instancias de CambioUsuario para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CambioUsuarioFactory
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
     * Crea una instancia simple de CambioUsuario con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CambioUsuario
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCambioUsuario = new CambioUsuario();
        $oCambioUsuario->setId_item($id);

        $oCambioUsuario->setId_schema_cambio(1001);
        $oCambioUsuario->setId_item_cambio(134);
        $oCambioUsuario->setId_usuario(12);
        $oCambioUsuario->setSfsv(1);
        $oCambioUsuario->setAvisoTipoVo(new AvisoTipoId(1));

        return $oCambioUsuario;
    }

    /**
     * Crea una instancia de CambioUsuario con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CambioUsuario
     */
    public function create(?int $id = null): CambioUsuario
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCambioUsuario = new CambioUsuario();
        $oCambioUsuario->setId_item($id);

        $oCambioUsuario->setId_schema_cambio($faker->numberBetween(1001, 3900));
        $oCambioUsuario->setId_item_cambio($faker->numberBetween(1, 1000));
        $oCambioUsuario->setId_usuario($faker->numberBetween(12, 100));
        $oCambioUsuario->setSfsv($faker->numberBetween(1, 2));
        $oCambioUsuario->setAvisoTipoVo(new AvisoTipoId($faker->numberBetween(1, 2)));
        $oCambioUsuario->setAvisado($faker->boolean);

        return $oCambioUsuario;
    }

    /**
     * Crea múltiples instancias de CambioUsuario
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

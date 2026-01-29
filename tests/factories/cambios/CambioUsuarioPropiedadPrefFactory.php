<?php

namespace Tests\factories\cambios;

use Faker\Factory;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\cambios\domain\value_objects\OperadorPref;
use src\cambios\domain\value_objects\PropiedadNombre;

/**
 * Factory para crear instancias de CambioUsuarioPropiedadPref para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CambioUsuarioPropiedadPrefFactory
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
     * Crea una instancia simple de CambioUsuarioPropiedadPref con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CambioUsuarioPropiedadPref
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        $oCambioUsuarioPropiedadPref->setId_item($id);

        // objetoPref existente para foreign keys
        $oCambioUsuarioPropiedadPref->setId_item_usuario_objeto(1111);

        $oCambioUsuarioPropiedadPref->setPropiedadVo(new PropiedadNombre('test_propiedad_vo'));
        $oCambioUsuarioPropiedadPref->setOperadorVo(null);

        return $oCambioUsuarioPropiedadPref;
    }

    /**
     * Crea una instancia de CambioUsuarioPropiedadPref con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CambioUsuarioPropiedadPref
     */
    public function create(?int $id = null): CambioUsuarioPropiedadPref
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        $oCambioUsuarioPropiedadPref->setId_item($id);

        // objetoPref existente para foreign keys
        $oCambioUsuarioPropiedadPref->setId_item_usuario_objeto(1111);

        $oCambioUsuarioPropiedadPref->setPropiedadVo(new PropiedadNombre($faker->word));
        $oCambioUsuarioPropiedadPref->setOperadorVo(new OperadorPref($faker->word));
        $oCambioUsuarioPropiedadPref->setValor($faker->word);
        $oCambioUsuarioPropiedadPref->setValor_old($faker->boolean);
        $oCambioUsuarioPropiedadPref->setValor_new($faker->boolean);

        return $oCambioUsuarioPropiedadPref;
    }

    /**
     * Crea múltiples instancias de CambioUsuarioPropiedadPref
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

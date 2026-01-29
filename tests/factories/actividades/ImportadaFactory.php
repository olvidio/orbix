<?php

namespace Tests\factories\actividades;

use Faker\Factory;
use src\actividades\domain\entity\Importada;

/**
 * Factory para crear instancias de Importada para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ImportadaFactory
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
     * Crea una instancia simple de Importada con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Importada
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oImportada = new Importada();
        $oImportada->setId_activ($id);


        return $oImportada;
    }

    /**
     * Crea una instancia de Importada con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Importada
     */
    public function create(?int $id = null): Importada
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oImportada = new Importada();
        $oImportada->setId_activ($id);


        return $oImportada;
    }

    /**
     * Crea múltiples instancias de Importada
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

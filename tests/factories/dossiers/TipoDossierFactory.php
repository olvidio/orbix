<?php

namespace Tests\factories\dossiers;

use Faker\Factory;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierApp;
use src\dossiers\domain\value_objects\TipoDossierCampoTo;
use src\dossiers\domain\value_objects\TipoDossierClass;
use src\dossiers\domain\value_objects\TipoDossierDb;
use src\dossiers\domain\value_objects\TipoDossierDescripcion;
use src\dossiers\domain\value_objects\TipoDossierTablaFrom;
use src\dossiers\domain\value_objects\TipoDossierTablaTo;

/**
 * Factory para crear instancias de TipoDossier para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TipoDossierFactory
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
     * Crea una instancia simple de TipoDossier con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TipoDossier
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTipoDossier = new TipoDossier();
        $oTipoDossier->setId_tipo_dossier($id);

        $oTipoDossier->setTablaFromVo(new TipoDossierTablaFrom('p'));
        $oTipoDossier->setPermiso_lectura(1);
        $oTipoDossier->setDepende_modificar(false);

        return $oTipoDossier;
    }

    /**
     * Crea una instancia de TipoDossier con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TipoDossier
     */
    public function create(?int $id = null): TipoDossier
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTipoDossier = new TipoDossier();
        $oTipoDossier->setId_tipo_dossier($id);

        $oTipoDossier->setDescripcionVo(new TipoDossierDescripcion($faker->sentence));
        $oTipoDossier->setTablaFromVo(new TipoDossierTablaFrom($faker->randomElement(['a','p','u'])));
        $oTipoDossier->setTablaToVo(new TipoDossierTablaTo($faker->word));
        $oTipoDossier->setCampoToVo(new TipoDossierCampoTo($faker->word));
        $oTipoDossier->setId_tipo_dossier_rel($faker->numberBetween(1, 1000));
        $oTipoDossier->setPermiso_lectura($faker->randomDigit());
        $oTipoDossier->setPermiso_escritura($faker->numberBetween(1, 1000));
        $oTipoDossier->setDepende_modificar($faker->boolean);
        $oTipoDossier->setAppVo(new TipoDossierApp($faker->word));
        $oTipoDossier->setClassVo(new TipoDossierClass($faker->word));
        $oTipoDossier->setDbVo(new TipoDossierDb($faker->numberBetween(1, 3)));

        return $oTipoDossier;
    }

    /**
     * Crea múltiples instancias de TipoDossier
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

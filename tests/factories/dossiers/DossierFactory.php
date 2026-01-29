<?php

namespace Tests\factories\dossiers;

use Faker\Factory;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\dossiers\domain\value_objects\DossierTabla;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de Dossier para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class DossierFactory
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
     * Crea una instancia simple de Dossier con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?DossierPk $pk = null): Dossier
    {
        $oDossier = new Dossier();

        if ($pk === null) {
            $oDossier->setTabla('p');
            $oDossier->setId_pau(10011);
            $oDossier->setId_tipo_dossier(3005);
        } else {
            $oDossier->setId_pau($pk->idPau());
            $oDossier->setTabla($pk->tabla());
            $oDossier->setId_tipo_dossier($pk->idTipoDossier());
        }

        $oDossier->setActive(false);

        return $oDossier;
    }

    /**
     * Crea una instancia de Dossier con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Dossier
     */
    public function create(?DossierPk $pk = null): Dossier
    {
        $faker = Factory::create('es_ES');

        $oDossier = new Dossier();
        if ($pk === null) {
            $oDossier->setTablaVo(new DossierTabla($faker->randomElement(['p', 'a', 'u'])));
            $oDossier->setId_pau($faker->numberBetween(10011, 100000));
            $oDossier->setId_tipo_dossier($faker->numberBetween(1, 1000));
        } else {
            $oDossier->setId_pau($pk->idPau());
            $oDossier->setTabla($pk->tabla());
            $oDossier->setId_tipo_dossier($pk->idTipoDossier());
        }

        $oDossier->setF_ini(new DateTimeLocal($faker->date()));
        $oDossier->setF_camb_dossier(new DateTimeLocal($faker->date()));
        $oDossier->setActive($faker->boolean);
        $oDossier->setF_active(new DateTimeLocal($faker->date()));

        return $oDossier;
    }

    /**
     * Crea múltiples instancias de Dossier
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

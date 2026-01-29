<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Factory para crear instancias de EncargoSacdHorario para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoSacdHorarioFactory
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
     * Crea una instancia simple de EncargoSacdHorario con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoSacdHorario
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_item($id);

        $oEncargoSacdHorario->setId_enc(3);
        $oEncargoSacdHorario->setId_nom(10011);
        $oEncargoSacdHorario->setF_ini(new DateTimeLocal('2023-04-05'));
        $oEncargoSacdHorario->setId_item_tarea_sacd(1000);

        return $oEncargoSacdHorario;
    }

    /**
     * Crea una instancia de EncargoSacdHorario con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoSacdHorario
     */
    public function create(?int $id = null): EncargoSacdHorario
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoSacdHorario = new EncargoSacdHorario();
        $oEncargoSacdHorario->setId_item($id);

        $oEncargoSacdHorario->setId_enc($faker->numberBetween(1, 1000));
        $oEncargoSacdHorario->setId_nom($faker->numberBetween(10011, 100000));
        $oEncargoSacdHorario->setF_ini(new DateTimeLocal($faker->date()));
        $oEncargoSacdHorario->setF_fin(new DateTimeLocal($faker->date()));
        $oEncargoSacdHorario->setDiaRefVo(new DiaRefCode($faker->randomLetter));
        $oEncargoSacdHorario->setDia_num($faker->numberBetween(1, 10));
        $oEncargoSacdHorario->setMasMenosVo(new MasMenosCode($faker->randomElement(['+','-'])));
        $oEncargoSacdHorario->setDia_inc($faker->numberBetween(1, 100));
        $oEncargoSacdHorario->setH_ini(TimeLocal::fromString($faker->Time()));
        $oEncargoSacdHorario->setH_fin(TimeLocal::fromString($faker->Time()));
        $oEncargoSacdHorario->setId_item_tarea_sacd($faker->numberBetween(1, 1000));

        return $oEncargoSacdHorario;
    }

    /**
     * Crea múltiples instancias de EncargoSacdHorario
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

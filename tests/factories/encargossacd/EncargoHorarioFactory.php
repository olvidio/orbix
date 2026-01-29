<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\encargossacd\domain\value_objects\MesNum;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Factory para crear instancias de EncargoHorario para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoHorarioFactory
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
     * Crea una instancia simple de EncargoHorario con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): EncargoHorario
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setId_item_h($id);

        $oEncargoHorario->setId_enc(1);
        $oEncargoHorario->setF_ini(new DateTimeLocal('2023-04-05'));
        $oEncargoHorario->setDiaRefVo(new DiaRefCode('t'));
        $oEncargoHorario->setMasMenosVo(new MasMenosCode('+'));
        $oEncargoHorario->setMesVo(new MesNum(10));

        return $oEncargoHorario;
    }

    /**
     * Crea una instancia de EncargoHorario con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoHorario
     */
    public function create(?int $id = null): EncargoHorario
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargoHorario = new EncargoHorario();
        $oEncargoHorario->setId_item_h($id);

        $oEncargoHorario->setId_enc($faker->numberBetween(1, 1000));
        $oEncargoHorario->setF_ini(new DateTimeLocal($faker->date()));
        $oEncargoHorario->setF_fin(new DateTimeLocal($faker->date()));
        $oEncargoHorario->setDiaRefVo(new DiaRefCode($faker->randomLetter));
        $oEncargoHorario->setDia_num($faker->numberBetween(1, 10));
        $oEncargoHorario->setMasMenosVo(new MasMenosCode($faker->randomElement(['+','-'])));
        $oEncargoHorario->setDia_inc($faker->numberBetween(1, 1000));
        $oEncargoHorario->setH_ini(TimeLocal::fromString($faker->Time()));
        $oEncargoHorario->setH_fin(TimeLocal::fromString($faker->Time()));
        $oEncargoHorario->setN_sacd($faker->numberBetween(1, 10));
        $oEncargoHorario->setMesVo(new MesNum($faker->numberBetween(1, 12)));

        return $oEncargoHorario;
    }

    /**
     * Crea múltiples instancias de EncargoHorario
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

<?php

namespace Tests\factories\misas;

use Faker\Factory;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoCtrId;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Factory para crear instancias de EncargoDia para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoDiaFactory
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
     * Crea una instancia simple de EncargoDia con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): EncargoDia
    {
        $uuid_itemVo = $id ?? EncargoDiaId::random();
        $oEncargoDia = new EncargoDia();
        $oEncargoDia->setUuidItemVo($uuid_itemVo);

        $oEncargoDia->setId_enc(103);
        $oEncargoDia->setTstart(TimeLocal::fromString('12:00'));
        $oEncargoDia->setTend(TimeLocal::fromString('15:30'));
        $oEncargoDia->setStatusVo(new EncargoDiaStatus(2));

        return $oEncargoDia;
    }

    /**
     * Crea una instancia de EncargoDia con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return EncargoDia
     */
    public function create(?string $id = null): EncargoDia
    {
        $faker = Factory::create('es_ES');

        $uuid_itemVo = $id ?? EncargoDiaId::random();
        $oEncargoDia = new EncargoDia();
        $oEncargoDia->setUuidItemVo($uuid_itemVo);

        $oEncargoDia->setId_enc($faker->numberBetween(1, 100));
        $oEncargoDia->setTstart(TimeLocal::fromString($faker->Time()));
        $oEncargoDia->setTend(TimeLocal::fromString($faker->Time()));
        $oEncargoDia->setId_nom($faker->numberBetween(10011, 1000000));
        $oEncargoDia->setObserv($faker->word);
        $oEncargoDia->setStatusVo(new EncargoDiaStatus($faker->numberBetween(1, 3)));

        return $oEncargoDia;
    }

    /**
     * Crea múltiples instancias de EncargoDia
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

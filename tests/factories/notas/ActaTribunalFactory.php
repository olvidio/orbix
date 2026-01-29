<?php

namespace Tests\factories\notas;

use Faker\Factory;
use src\notas\domain\entity\ActaTribunal;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Examinador;
use src\notas\domain\value_objects\Orden;

/**
 * Factory para crear instancias de ActaTribunal para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActaTribunalFactory
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
     * Crea una instancia simple de ActaTribunal con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActaTribunal
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActaTribunal = new ActaTribunal();
        $oActaTribunal->setId_item($id);
        $oActaTribunal->setActaVo(new ActaNumero('dlb 23/50'));


        return $oActaTribunal;
    }

    /**
     * Crea una instancia de ActaTribunal con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActaTribunal
     */
    public function create(?int $id = null): ActaTribunal
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oActaTribunal = new ActaTribunal();
        $oActaTribunal->setId_item($id);

        $oActaTribunal->setActaVo(new ActaNumero(substr($faker->word, 0, 6). " ". $faker->numberBetween(1, 100)."/".$faker->numberBetween(20, 30)));
        $oActaTribunal->setExaminadorVo(new Examinador($faker->word));
        $oActaTribunal->setOrdenVo(new Orden($faker->numberBetween(1, 10)));

        return $oActaTribunal;
    }

    /**
     * Crea múltiples instancias de ActaTribunal
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

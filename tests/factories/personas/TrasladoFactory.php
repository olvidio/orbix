<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\personas\domain\entity\Traslado;
use src\personas\domain\value_objects\NombreCentroText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\TrasladoTipoCmbCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de Traslado para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TrasladoFactory
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
     * Crea una instancia simple de Traslado con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Traslado
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTraslado = new Traslado();
        $oTraslado->setId_item($id);

        $oTraslado->setId_nom(10011);
        $oTraslado->setTipoCmbVo(new TrasladoTipoCmbCode('test'));
        $oTraslado->setCtrDestinoVo(new NombreCentroText('test centro dst'));

        return $oTraslado;
    }

    /**
     * Crea una instancia de Traslado con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Traslado
     */
    public function create(?int $id = null): Traslado
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTraslado = new Traslado();
        $oTraslado->setId_item($id);

        $oTraslado->setId_nom($faker->numberBetween(1, 1000));
        $oTraslado->setF_traslado(new DateTimeLocal($faker->date()));
        $oTraslado->setTipoCmbVo(new TrasladoTipoCmbCode(substr($faker->word, 0, 4)));
        $oTraslado->setId_ctr_origen($faker->numberBetween(10011, 50000));
        $oTraslado->setCtrOrigenVo(new NombreCentroText($faker->word));
        $oTraslado->setId_ctr_destino($faker->numberBetween(10011, 50000));
        $oTraslado->setCtrDestinoVo(new NombreCentroText($faker->word));
        $oTraslado->setObservVo(new ObservText($faker->realText(255)));

        return $oTraslado;
    }

    /**
     * Crea múltiples instancias de Traslado
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

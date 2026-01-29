<?php

namespace Tests\factories\notas;

use Faker\Factory;
use src\notas\domain\entity\PersonaNota;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Detalle;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaMax;
use src\notas\domain\value_objects\NotaNum;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\PersonaNotaPk;
use src\notas\domain\value_objects\TipoActa;
use src\procesos\domain\value_objects\ActividadId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de PersonaNota para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaNotaFactory
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
     * Crea una instancia simple de PersonaNota con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?PersonaNotaPk $pk = null): PersonaNota
    {
        $oPersonaNota = new PersonaNota();

        if ($pk === null) {
            $oPersonaNota->setId_nom(10011);
            $oPersonaNota->setId_nivel(1223);
            $oPersonaNota->setTipoActaVo(1);
        } else {
            $oPersonaNota->setId_nom($pk->IdNom());
            $oPersonaNota->setId_nivel($pk->IdNivel());
            $oPersonaNota->setTipoActaVo(new TipoActa($pk->TipoActa()));
        }
        $oPersonaNota->setId_schema(1001);
        $oPersonaNota->setIdAsignaturaVo(new AsignaturaId(1224));
        $oPersonaNota->setIdSituacionVo(new NotaSituacion(3));
        $oPersonaNota->setActaVo(new ActaNumero('dlb 30/50'));
        $oPersonaNota->setF_acta(new DateTimeLocal('2025-10-23'));
        $oPersonaNota->setDetalleVo(new Detalle('test_detalle_vo'));
        $oPersonaNota->setPreceptor(false);
        $oPersonaNota->setId_preceptor(10011);
        $oPersonaNota->setEpocaVo(new NotaEpoca(2));
        $oPersonaNota->setIdActivVo(new ActividadId(123456));
        $oPersonaNota->setNotaNumVo(new NotaNum(8.5));
        $oPersonaNota->setNotaMaxVo(new NotaMax(10));

        return $oPersonaNota;
    }

    /**
     * Crea una instancia de PersonaNota con datos realistas usando Faker
     * @return PersonaNota
     */
    public function create(?PersonaNotaPk $pk = null): PersonaNota
    {
        $faker = Factory::create('es_ES');

        $oPersonaNota = new PersonaNota();

        if ($pk === null) {
            $oPersonaNota->setId_nom($faker->numberBetween(10011, 100000));
            $oPersonaNota->setId_nivel($faker->numberBetween(1000, 3999));
            $oPersonaNota->setTipoActaVo(new TipoActa($faker->numberBetween(1, 2)));
        } else {
            $oPersonaNota->setId_nom($pk->IdNom());
            $oPersonaNota->setId_nivel($pk->IdNivel());
            $oPersonaNota->setTipoActaVo(new TipoActa($pk->TipoActa()));
        }
        $oPersonaNota->setId_schema(1001);
        $oPersonaNota->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1000, 3999)));
        $oPersonaNota->setIdSituacionVo(new NotaSituacion($faker->numberBetween(1, 10)));
        $oPersonaNota->setActaVo(new ActaNumero(substr($faker->word, 0, 6). " ". $faker->numberBetween(1, 100)."/".$faker->numberBetween(20, 30)));
        $oPersonaNota->setF_acta(new DateTimeLocal($faker->date()));
        $oPersonaNota->setDetalleVo(new Detalle($faker->word));
        $oPersonaNota->setPreceptor($faker->boolean);
        $oPersonaNota->setId_preceptor($faker->numberBetween(10011, 100000));
        $oPersonaNota->setEpocaVo(new NotaEpoca($faker->numberBetween(1, 10)));
        $oPersonaNota->setId_activ($faker->numberBetween(100011, 3000000));
        $oPersonaNota->setIdActivVo(new ActividadId(123456));
        $oPersonaNota->setNotaNumVo(new NotaNum($faker->randomFloat(2, 0, 100)));
        $oPersonaNota->setNotaMaxVo(new NotaMax($faker->numberBetween(1, 10)));

        return $oPersonaNota;
    }

    /**
     * Crea múltiples instancias de PersonaNota
     * @param int $count Número de instancias a crear
     * @return array
     */
    public function createMany(int $count): array
    {
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create();
        }

        return $instances;
    }
}

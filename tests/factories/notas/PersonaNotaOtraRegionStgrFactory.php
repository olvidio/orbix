<?php

namespace Tests\factories\notas;

use Faker\Factory;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
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
 * Factory para crear instancias de PersonaNotaOtraRegionStgr para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaNotaOtraRegionStgrFactory
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
     * Crea una instancia simple de PersonaNotaOtraRegionStgr con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?PersonaNotaPk $pk = null): PersonaNotaOtraRegionStgr
    {
        $oPersonaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();

        if ($pk === null) {
            $oPersonaNotaOtraRegionStgr->setId_nom(10011);
            $oPersonaNotaOtraRegionStgr->setId_nivel(1223);
            $oPersonaNotaOtraRegionStgr->setTipoActaVo(new TipoActa(1));
        } else {
            $oPersonaNotaOtraRegionStgr->setId_nom($pk->IdNom());
            $oPersonaNotaOtraRegionStgr->setId_nivel($pk->IdNivel());
            $oPersonaNotaOtraRegionStgr->setTipoActaVo(new TipoActa($pk->TipoActa()));
        }
        $oPersonaNotaOtraRegionStgr->setId_schema(1001);
        $oPersonaNotaOtraRegionStgr->setIdAsignaturaVo(new AsignaturaId(1224));
        $oPersonaNotaOtraRegionStgr->setIdSituacionVo(new NotaSituacion(3));
        $oPersonaNotaOtraRegionStgr->setActaVo(new ActaNumero('dlb 30/50'));
        $oPersonaNotaOtraRegionStgr->setF_acta(new DateTimeLocal('2025-10-23'));
        $oPersonaNotaOtraRegionStgr->setDetalleVo(new Detalle('test_detalle_vo'));
        $oPersonaNotaOtraRegionStgr->setPreceptor(false);
        $oPersonaNotaOtraRegionStgr->setId_preceptor(10011);
        $oPersonaNotaOtraRegionStgr->setEpocaVo(new NotaEpoca(2));
        $oPersonaNotaOtraRegionStgr->setIdActivVo(new ActividadId(123456));
        $oPersonaNotaOtraRegionStgr->setNotaNumVo(new NotaNum(8.5));
        $oPersonaNotaOtraRegionStgr->setNotaMaxVo(new NotaMax(10));
        $oPersonaNotaOtraRegionStgr->setJson_certificados([]);

        return $oPersonaNotaOtraRegionStgr;
    }

    /**
     * Crea una instancia de PersonaNotaOtraRegionStgr con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaNotaOtraRegionStgr
     */
    public function create(?PersonaNotaPk $pk = null): PersonaNotaOtraRegionStgr
    {
        $faker = Factory::create('es_ES');

        $oPersonaNotaOtraRegionStgr = new PersonaNotaOtraRegionStgr();

        if ($pk === null) {
            $oPersonaNotaOtraRegionStgr->setId_nom($faker->numberBetween(10011, 100000));
            $oPersonaNotaOtraRegionStgr->setId_nivel($faker->numberBetween(1000, 3999));
            $oPersonaNotaOtraRegionStgr->setTipoActaVo(new TipoActa($faker->numberBetween(1, 2)));
        } else {
            $oPersonaNotaOtraRegionStgr->setId_nom($pk->IdNom());
            $oPersonaNotaOtraRegionStgr->setId_nivel($pk->IdNivel());
            $oPersonaNotaOtraRegionStgr->setTipoActaVo(new TipoActa($pk->TipoActa()));
        }
        $oPersonaNotaOtraRegionStgr->setId_schema(1001);
        $oPersonaNotaOtraRegionStgr->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1000, 3999)));
        $oPersonaNotaOtraRegionStgr->setIdSituacionVo(new NotaSituacion($faker->numberBetween(1, 10)));
        $oPersonaNotaOtraRegionStgr->setActaVo(new ActaNumero(substr($faker->word, 0, 6) . " " . $faker->numberBetween(1, 100) . "/" . $faker->numberBetween(20, 30)));
        $oPersonaNotaOtraRegionStgr->setF_acta(new DateTimeLocal($faker->date()));
        $oPersonaNotaOtraRegionStgr->setDetalleVo(new Detalle($faker->word));
        $oPersonaNotaOtraRegionStgr->setPreceptor($faker->boolean);
        $oPersonaNotaOtraRegionStgr->setId_preceptor($faker->numberBetween(10011, 100000));
        $oPersonaNotaOtraRegionStgr->setEpocaVo(new NotaEpoca($faker->numberBetween(1, 10)));
        $oPersonaNotaOtraRegionStgr->setId_activ($faker->numberBetween(100011, 3000000));
        $oPersonaNotaOtraRegionStgr->setIdActivVo(new ActividadId(123456));
        $oPersonaNotaOtraRegionStgr->setNotaNumVo(new NotaNum($faker->randomFloat(2, 0, 100)));
        $oPersonaNotaOtraRegionStgr->setNotaMaxVo(new NotaMax($faker->numberBetween(1, 10)));
        $oPersonaNotaOtraRegionStgr->setJson_certificados([]);

        return $oPersonaNotaOtraRegionStgr;
    }

    /**
     * Crea múltiples instancias de PersonaNotaOtraRegionStgr
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

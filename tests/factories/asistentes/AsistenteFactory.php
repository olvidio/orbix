<?php

namespace Tests\factories\asistentes;

use Faker\Factory;
use src\asistentes\domain\entity\Asistente;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\value_objects\AsistenteEncargo;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\value_objects\AsistenteObservEst;
use src\asistentes\domain\value_objects\AsistentePk;
use src\asistentes\domain\value_objects\AsistentePropietario;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * Factory para crear instancias de Asistente para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class AsistenteFactory
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
     * Crea una instancia simple de Asistente con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?AsistentePk $pk = null): Asistente
    {
        $oAsistente = new Asistente();
          if ($pk === null) {
            $oAsistente->setId_activ(10018001);
            $oAsistente->setId_nom(10019001);
        } else {
            $oAsistente->setId_activ($pk->idActiv());
            $oAsistente->setId_nom($pk->idNom());
        }

        $oAsistente->setPropio(false);
        $oAsistente->setEst_ok(false);
        $oAsistente->setCfi(false);
        $oAsistente->setFalta(false);

        return $oAsistente;
    }

    /**
     * Crea una instancia de Asistente con datos realistas usando Faker
     * @param AsistentePk|null $pk ID específico o null para generar uno aleatorio
     * @return Asistente
     */
    public function create(?AsistentePk $pk = null): Asistente
    {
        $faker = Factory::create('es_ES');

        $oAsistente = new Asistente();
        if ($pk === null) {
            $oAsistente->setId_activ(10018001);
            $oAsistente->setId_nom(10019001);
        } else {
            $oAsistente->setId_activ($pk->idActiv());
            $oAsistente->setId_nom($pk->idNom());
        }

        $oAsistente->setPropio($faker->boolean);
        $oAsistente->setEst_ok($faker->boolean);
        $oAsistente->setCfi($faker->boolean);
        $oAsistente->setCfi_con($faker->numberBetween(10011, 100000));
        $oAsistente->setFalta($faker->boolean);
        $oAsistente->setEncargoVo(new AsistenteEncargo($faker->word));
        $oAsistente->setDlResponsableVo(new DelegacionCode(substr($faker->word(), 0, 8)));
        $oAsistente->setObservVo(new AsistenteObserv($faker->text(200)));
        $oAsistente->setIdTablaVo(new PersonaTablaCode('dl'));
        $oAsistente->setPlazaVo(new PlazaId($faker->numberBetween(1, 100)));
        $oAsistente->setPropietarioVo(new AsistentePropietario($faker->word));
        $oAsistente->setObservEstVo(new AsistenteObservEst($faker->text));

        return $oAsistente;
    }

    /**
     * Crea múltiples instancias de Asistente
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

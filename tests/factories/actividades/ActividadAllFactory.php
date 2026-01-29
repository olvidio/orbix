<?php

namespace Tests\factories\actividades;

use Faker\Factory;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\ActividadDescText;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadObserv;
use src\actividades\domain\value_objects\ActividadObservMaterial;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\Plazas;
use src\usuarios\domain\value_objects\IdLocale;

/**
 * Factory para crear instancias de ActividadAll para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActividadAllFactory
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
     * Crea una instancia simple de ActividadAll con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ActividadAll
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oActividadAll = new ActividadAll();
        $oActividadAll->setId_activ($id);

        $oActividadAll->setIdTipoActivVo(new ActividadTipoId(271000));
        $oActividadAll->setNomActivVo(new ActividadNomText('test_nom_activ_vo'));
        $oActividadAll->setStatusVo(new StatusId(2));
        $oActividadAll->setF_ini(new DateTimeLocal('2024-05-01'));
        $oActividadAll->setF_fin(new DateTimeLocal('2024-05-23'));
        $oActividadAll->setDlOrgVo(new DelegacionCode('dlb'));
        $oActividadAll->setIdTablaVo(new IdTablaCode('dl'));

        return $oActividadAll;
    }

    /**
     * Crea una instancia de ActividadAll con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ActividadAll
     */
    public function create(?int $id = null): ActividadAll
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(100133, 3339999));

        $oActividadAll = new ActividadAll();
        $oActividadAll->setId_activ($id);

        $oActividadAll->setIdTipoActivVo(new ActividadTipoId($faker->numerify('######')));
        $oActividadAll->setDlOrgVo(new DelegacionCode(substr($faker->word(),0,7)));
        $oActividadAll->setNomActivVo(new ActividadNomText($faker->word));
        $oActividadAll->setId_ubi($faker->numberBetween(10022, 100000));
        $oActividadAll->setDescActivVo(new ActividadDescText($faker->text(80)));
        $oActividadAll->setF_ini(new DateTimeLocal($faker->date()));
        $oActividadAll->setH_ini(TimeLocal::fromString($faker->time()));
        $oActividadAll->setF_fin(new DateTimeLocal($faker->date()));
        $oActividadAll->setH_fin(TimeLocal::fromString($faker->time()));
        $oActividadAll->setTipo_horario($faker->numberBetween(1, 1000));
        $oActividadAll->setPrecioVo(new Dinero($faker->randomFloat(2, 0, 5000)));
        $oActividadAll->setNum_asistentes($faker->numberBetween(1, 1000));
        $oActividadAll->setStatusVo(new StatusId($faker->randomKey(StatusId::getArrayStatus())));
        $oActividadAll->setObservVo(new ActividadObserv($faker->text()));
        $oActividadAll->setNivelStgrVo(new NivelStgrId($faker->randomKey(NivelStgrId::getArrayNivelStgr())));
        $oActividadAll->setObserv_materialVo(new ActividadObservMaterial($faker->text()));
        $oActividadAll->setLugar_esp($faker->text());
        $oActividadAll->setTarifaVo(new TarifaId($faker->numberBetween(1, 10)));
        $oActividadAll->setIdRepeticionVo(new RepeticionId($faker->numberBetween(1, 10)));
        $oActividadAll->setPublicado($faker->boolean);
        $oActividadAll->setIdTablaVo(new IdTablaCode($faker->randomElement(['dl', 'ex'])));
        $oActividadAll->setPlazasVo(new Plazas($faker->numberBetween(1, 100)));
        $oActividadAll->setIdiomaVo(new IdLocale($faker->locale().'.UTF-8'));

        return $oActividadAll;
    }

    /**
     * Crea múltiples instancias de ActividadAll
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

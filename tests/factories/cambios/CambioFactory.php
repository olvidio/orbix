<?php

namespace Tests\factories\cambios;

use Faker\Factory;
use src\cambios\domain\entity\Cambio;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\PropiedadNombre;
use src\cambios\domain\value_objects\TipoCambioId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * Factory para crear instancias de Cambio para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CambioFactory
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
     * Crea una instancia simple de Cambio con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Cambio
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCambio = new Cambio();
        $oCambio->setId_schema($id);

        $oCambio->setId_item_cambio(122);
        $oCambio->setTipoCambioVo(new TipoCambioId(2));
        $oCambio->setId_activ(1);
        $oCambio->setIdTipoActivVo(new ActividadTipoId(123456));
        $oCambio->setIdStatusVo(new StatusId(2));
        $oCambio->setDlOrgVo(new DelegacionCode('dlorg'));
        $oCambio->setObjetoVo(new ObjetoNombre('test_objeto_vo'));
        $oCambio->setPropiedadVo(new PropiedadNombre('test_propiedad_vo'));
        $oCambio->setTimestamp_cambio(new DateTimeLocal('now'));

        return $oCambio;
    }

    /**
     * Crea una instancia de Cambio con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Cambio
     */
    public function create(?int $id = null): Cambio
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCambio = new Cambio();
        $oCambio->setId_schema($id);

        $oCambio->setId_item_cambio($faker->numberBetween(1, 1000));
        $oCambio->setTipoCambioVo(new TipoCambioId($faker->numberBetween(1, 2)));
        $oCambio->setId_activ($faker->numberBetween(30011, 300000));
        $oCambio->setIdTipoActivVo(new ActividadTipoId($faker->numerify('######')));
        $oCambio->setJson_fases_sv(json_encode($faker->shuffleArray([1, 16, 3]), JSON_THROW_ON_ERROR));
        $oCambio->setJson_fases_sf(json_encode($faker->shuffleArray([1, 16, 3]), JSON_THROW_ON_ERROR));
        $oCambio->setIdStatusVo(new StatusId($faker->randomKey(StatusId::getArrayStatus())));
        $oCambio->setDlOrgVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oCambio->setObjetoVo(new ObjetoNombre($faker->word));
        $oCambio->setPropiedadVo(new PropiedadNombre($faker->word));
        $oCambio->setValor_old($faker->word);
        $oCambio->setValor_new($faker->word);
        $oCambio->setQuien_cambia($faker->numberBetween(1, 1000));
        $oCambio->setSfsv_quien_cambia($faker->randomElement([1, 2]));
        $oCambio->setTimestamp_cambio(new DateTimeLocal('now'));

        return $oCambio;
    }

    /**
     * Crea múltiples instancias de Cambio
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

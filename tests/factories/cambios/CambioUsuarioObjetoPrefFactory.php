<?php

namespace Tests\factories\cambios;

use Faker\Factory;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\CsvPauId;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * Factory para crear instancias de CambioUsuarioObjetoPref para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class CambioUsuarioObjetoPrefFactory
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
     * Crea una instancia simple de CambioUsuarioObjetoPref con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): CambioUsuarioObjetoPref
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
        $oCambioUsuarioObjetoPref->setId_item_usuario_objeto($id);

        $oCambioUsuarioObjetoPref->setId_usuario(1);
        $oCambioUsuarioObjetoPref->setDlOrgVo(new DelegacionCode('dlorg'));
        $oCambioUsuarioObjetoPref->setId_tipo_activ_txt('testid');
        $oCambioUsuarioObjetoPref->setId_fase_ref(1);
        $oCambioUsuarioObjetoPref->setAviso_off(false);
        $oCambioUsuarioObjetoPref->setAviso_on(true);
        $oCambioUsuarioObjetoPref->setAviso_outdate(false);
        $oCambioUsuarioObjetoPref->setObjetoVo(new ObjetoNombre('test_objeto_vo'));
        $oCambioUsuarioObjetoPref->setAvisoTipoVo(new AvisoTipoId(1));
        $oCambioUsuarioObjetoPref->setCsvIdPauVo(CsvPauId::fromNullableString('10011,10012'));

        return $oCambioUsuarioObjetoPref;
    }

    /**
     * Crea una instancia de CambioUsuarioObjetoPref con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return CambioUsuarioObjetoPref
     */
    public function create(?int $id = null): CambioUsuarioObjetoPref
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
        $oCambioUsuarioObjetoPref->setId_item_usuario_objeto($id);

        $oCambioUsuarioObjetoPref->setId_usuario($faker->numberBetween(1, 100));
        $oCambioUsuarioObjetoPref->setDlOrgVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oCambioUsuarioObjetoPref->setId_tipo_activ_txt($faker->word);
        $oCambioUsuarioObjetoPref->setId_fase_ref($faker->numberBetween(1, 1000));
        $oCambioUsuarioObjetoPref->setAviso_off($faker->boolean);
        $oCambioUsuarioObjetoPref->setAviso_on($faker->boolean);
        $oCambioUsuarioObjetoPref->setAviso_outdate($faker->date());
        $oCambioUsuarioObjetoPref->setObjetoVo(new ObjetoNombre($faker->word));
        $oCambioUsuarioObjetoPref->setAvisoTipoVo(new AvisoTipoId($faker->numberBetween(1, 2)));
        $oCambioUsuarioObjetoPref->setCsvIdPauVo(new CsvPauId("$faker->numberBetween(10011, 10000),$faker->numberBetween(10011, 10000)"));

        return $oCambioUsuarioObjetoPref;
    }

    /**
     * Crea múltiples instancias de CambioUsuarioObjetoPref
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

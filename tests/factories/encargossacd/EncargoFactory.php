<?php

namespace Tests\factories\encargossacd;

use Faker\Factory;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoDescText;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\encargossacd\domain\value_objects\EncargoOrden;
use src\encargossacd\domain\value_objects\EncargoPrioridad;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\LugarDescText;
use src\encargossacd\domain\value_objects\ObservText;
use src\shared\domain\value_objects\LocaleCode;

/**
 * Factory para crear instancias de Encargo para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class EncargoFactory
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
     * Crea una instancia simple de Encargo con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): Encargo
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oEncargo = new Encargo();
        $oEncargo->setId_enc($id);

        $oEncargo->setTipoEncVo(new EncargoTipoId(22));
        $oEncargo->setGrupoEncargoVo(new EncargoGrupo(5));
        $oEncargo->setDescEncVo(new EncargoDescText('test_desc_enc_vo'));
        $oEncargo->setIdiomaEncVo(new LocaleCode('ca_ES.UTF-8'));
        $oEncargo->setDescLugarVo(new LugarDescText('test_desc_lugar_vo'));
        $oEncargo->setObservVo(new ObservText('test_observ_vo'));
        $oEncargo->setOrdenVo(new EncargoOrden(10));
        $oEncargo->setPrioridadVo(new EncargoPrioridad(3));

        return $oEncargo;
    }

    /**
     * Crea una instancia de Encargo con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Encargo
     */
    public function create(?int $id = null): Encargo
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oEncargo = new Encargo();
        $oEncargo->setId_enc($id);

        $oEncargo->setTipoEncVo(new EncargoTipoId($faker->numberBetween(1, 100)));
        $oEncargo->setGrupoEncargoVo(new EncargoGrupo($faker->numberBetween(1, 10)));
        $oEncargo->setId_ubi($faker->numberBetween(3001231, 3000000));
        $oEncargo->setId_zona($faker->numberBetween(1, 100));
        $oEncargo->setDescEncVo(new EncargoDescText($faker->text(150)));
        $oEncargo->setIdiomaEncVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oEncargo->setDescLugarVo(new LugarDescText($faker->text(150)));
        $oEncargo->setObservVo(new ObservText($faker->text));
        $oEncargo->setOrdenVo(new EncargoOrden($faker->numberBetween(1, 10)));
        $oEncargo->setPrioridadVo(new EncargoPrioridad($faker->numberBetween(1, 10)));

        return $oEncargo;
    }

    /**
     * Crea múltiples instancias de Encargo
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

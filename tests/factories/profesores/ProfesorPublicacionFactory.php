<?php

namespace Tests\factories\profesores;

use Faker\Factory;
use src\profesores\domain\entity\ProfesorPublicacion;
use src\profesores\domain\value_objects\ColeccionName;
use src\profesores\domain\value_objects\EditorialName;
use src\profesores\domain\value_objects\LugarPublicacionName;
use src\profesores\domain\value_objects\ObservacionText;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\ReferenciaText;
use src\profesores\domain\value_objects\TipoPublicacionName;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory para crear instancias de ProfesorPublicacion para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ProfesorPublicacionFactory
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
     * Crea una instancia simple de ProfesorPublicacion con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): ProfesorPublicacion
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oProfesorPublicacion = new ProfesorPublicacion();
        $oProfesorPublicacion->setId_item($id);

        $oProfesorPublicacion->setId_nom(1);
        $oProfesorPublicacion->setTituloVo(new PublicacionTitulo('Título de prueba'));

        return $oProfesorPublicacion;
    }

    /**
     * Crea una instancia de ProfesorPublicacion con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return ProfesorPublicacion
     */
    public function create(?int $id = null): ProfesorPublicacion
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oProfesorPublicacion = new ProfesorPublicacion();
        $oProfesorPublicacion->setId_item($id);

        $oProfesorPublicacion->setTipoPublicacionVo(new TipoPublicacionName($faker->word));
        $oProfesorPublicacion->setTituloVo(new PublicacionTitulo($faker->word));
        $oProfesorPublicacion->setEditorialVo(new EditorialName($faker->word));
        $oProfesorPublicacion->setColeccionVo(new ColeccionName($faker->word));
        $oProfesorPublicacion->setReferenciaVo(new ReferenciaText($faker->word));
        $oProfesorPublicacion->setLugarVo(new LugarPublicacionName($faker->word));
        $oProfesorPublicacion->setObservVo(new ObservacionText($faker->word));
        $oProfesorPublicacion->setId_nom($faker->numberBetween(1, 1000));
        $oProfesorPublicacion->setF_publicacion(new DateTimeLocal($faker->date()));
        $oProfesorPublicacion->setPendiente($faker->boolean);

        return $oProfesorPublicacion;
    }

    /**
     * Crea múltiples instancias de ProfesorPublicacion
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

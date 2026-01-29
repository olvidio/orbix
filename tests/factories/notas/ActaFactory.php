<?php

namespace Tests\factories\notas;

use Faker\Factory;
use src\notas\domain\entity\Acta;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Libro;
use src\notas\domain\value_objects\Linea;
use src\notas\domain\value_objects\Lugar;
use src\notas\domain\value_objects\Observ;
use src\notas\domain\value_objects\Pagina;
use src\notas\domain\value_objects\Pdf;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

/**
 * Factory para crear instancias de Acta para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class ActaFactory
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
     * Crea una instancia simple de Acta con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?string $id = null): Acta
    {
        $id = $id ?? 'dlb '.(99 + random_int(10, 99)) .'/99';
        $oActa = new Acta();
        $oActa->setActa($id);

        $oActa->setActaVo(new ActaNumero('test 3/45'));
        $oActa->setIdAsignaturaVo(new AsignaturaId(2034));
        $oActa->setF_acta(new DateTimeLocal('2025-10-23'));

        return $oActa;
    }

    /**
     * Crea una instancia de Acta con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return Acta
     */
    public function create(?string $id = null): Acta
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? substr($faker->word, 0, 6). " ". $faker->numberBetween(1, 100)."/".$faker->numberBetween(20, 30);

        $oActa = new Acta();
        $oActa->setActaVo(new ActaNumero($id));

        $oActa->setId_activ($faker->numberBetween(1, 1000));
        $oActa->setIdAsignaturaVo(new AsignaturaId($faker->numberBetween(1000, 3999)));
        $oActa->setF_acta(new DateTimeLocal($faker->date()));
        $oActa->setLibroVo(new Libro($faker->numberBetween(1, 10)));
        $oActa->setPaginaVo(new Pagina($faker->numberBetween(1, 10)));
        $oActa->setLineaVo(new Linea($faker->numberBetween(1, 10)));
        $oActa->setLugarVo(new Lugar($faker->word));
        $oActa->setObservVo(new Observ($faker->word));
        $oActa->setPdfVo(new Pdf($faker->word));

        return $oActa;
    }

    /**
     * Crea múltiples instancias de Acta
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

<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\criteria\DireccionField;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

/**
 * `matching()` de los repositorios de direcciones de `ubis` (paso 4.4 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgUbisDireccionesMatchingTest extends myTest
{
    public function test_direccion_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(DireccionRepositoryInterface::class);
        $o = (new DireccionFactory())->create();
        $id = $o->getId_direccion();
        $repo->Guardar($o);

        $criteria = Criteria::create()
            ->where(Filter::eq(DireccionField::IdDireccion, $id))
            ->andWhere(Filter::matchesUnaccented(DireccionField::Poblacion, (string) $o->getPoblacion()))
            ->orderBy(DireccionField::Poblacion);
        $nuevo = $repo->matching($criteria);
        $legacy = $repo->getDirecciones(
            ['id_direccion' => $id, 'poblacion' => (string) $o->getPoblacion(), '_ordre' => 'poblacion'],
            ['poblacion' => 'sin_acentos']
        );

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_direccion_matching_c_p_subcadena_equivale_a_like(): void
    {
        $repo = $GLOBALS['container']->get(DireccionRepositoryInterface::class);
        $o = (new DireccionFactory())->create();
        $id = $o->getId_direccion();
        $repo->Guardar($o);
        $cp = (string) $o->getC_p();
        $fragmento = substr($cp, 1, 3);

        $nuevo = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(DireccionField::IdDireccion, $id))
                ->andWhere(Filter::matches(DireccionField::CP, preg_quote($fragmento)))
        );
        $legacy = $repo->getDirecciones(
            ['id_direccion' => $id, 'c_p' => '%' . $fragmento . '%'],
            ['c_p' => 'LIKE']
        );

        $this->assertCount(1, $nuevo);
        $this->assertEquals($legacy, $nuevo);

        $repo->Eliminar($o);
    }

    public function test_variantes_de_direccion_heredan_matching(): void
    {
        $casa = $GLOBALS['container']->get(DireccionCasaRepositoryInterface::class);
        $centroDl = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
        // solo ids válidos para el VO (hay filas de prueba antiguas con ids inválidos)
        $criteria = Criteria::create()
            ->where(Filter::between(DireccionField::IdDireccion, 10000, 39999))
            ->orderBy(DireccionField::IdDireccion)
            ->limit(5);

        $this->assertEquals($casa->getDirecciones(['id_direccion' => '10000,39999', '_ordre' => 'id_direccion', '_limit' => 5], ['id_direccion' => 'BETWEEN']), $casa->matching($criteria));
        $this->assertEquals($centroDl->getDirecciones(['id_direccion' => '10000,39999', '_ordre' => 'id_direccion', '_limit' => 5], ['id_direccion' => 'BETWEEN']), $centroDl->matching($criteria));
    }
}

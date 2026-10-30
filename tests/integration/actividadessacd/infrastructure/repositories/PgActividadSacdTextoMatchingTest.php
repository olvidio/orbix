<?php

namespace Tests\integration\actividadessacd\infrastructure\repositories;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\criteria\ActividadSacdTextoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de PgActividadSacdTextoRepository (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Compara con el método legacy sobre los datos existentes.
 */
class PgActividadSacdTextoMatchingTest extends myTest
{
    private ActividadSacdTextoRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
    }

    public function test_matching_ordenado_por_id_item(): void
    {
        $ids = array_map(
            fn ($o) => $o->getId_item(),
            $this->repository->matching(Criteria::create()->orderBy(ActividadSacdTextoField::IdItem))
        );
        $ordenados = $ids;
        sort($ordenados);

        $this->assertSame($ordenados, $ids);
    }

    public function test_matching_por_clave_e_idioma(): void
    {
        $textos = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(ActividadSacdTextoField::Clave, 'com_sacd'))
                ->andWhere(Filter::eq(ActividadSacdTextoField::Idioma, 'es_ES.UTF-8'))
        );

        foreach ($textos as $oTexto) {
            $this->assertSame('com_sacd', $oTexto->getClave());
            $this->assertSame('es_ES.UTF-8', $oTexto->getIdioma());
        }
    }
}

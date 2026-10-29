<?php

namespace Tests\integration\encargossacd\infrastructure\persistence\postgresql;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoField;
use src\encargossacd\domain\criteria\EncargoTextoField;
use src\encargossacd\domain\criteria\EncargoTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de `Encargo`, `EncargoTipo` y `EncargoTexto` (paso 4.5 de docs/dev/refactor_busquedas_criteria.md).
 */
class PgEncargosMatchingTest extends myTest
{
    public function test_encargo_tipo_regex_y_orden_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

        $nuevo = $repo->matching(
            Criteria::create()->where(Filter::matches(EncargoTipoField::IdTipoEnc, '^8...'))->orderBy(EncargoTipoField::IdTipoEnc)
        );
        $legacy = $repo->getEncargoTipos(['id_tipo_enc' => '^8...', '_ordre' => 'id_tipo_enc'], ['id_tipo_enc' => '~']);

        $this->assertEquals($legacy, $nuevo);
    }

    public function test_encargo_tipo_sin_acentos_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

        $this->assertEquals(
            $repo->getEncargoTipos(['tipo_enc' => 'a', '_ordre' => 'id_tipo_enc'], ['tipo_enc' => 'sin_acentos']),
            $repo->matching(
                Criteria::create()->where(Filter::matchesUnaccented(EncargoTipoField::TipoEnc, 'a'))->orderBy(EncargoTipoField::IdTipoEnc)
            )
        );
    }

    public function test_encargo_in_y_regex_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        $this->assertEquals(
            $repo->getEncargos(['id_tipo_enc' => '(1|2|3).0', '_ordre' => 'id_enc'], ['id_tipo_enc' => '~']),
            $repo->matching(
                Criteria::create()->where(Filter::matches(EncargoField::IdTipoEnc, '(1|2|3).0'))->orderBy(EncargoField::IdEnc)
            )
        );
        $this->assertEquals(
            $repo->getEncargos(['id_tipo_enc' => '{8100, 8200, 1100}', '_ordre' => 'id_enc'], ['id_tipo_enc' => 'ANY']),
            $repo->matching(
                Criteria::create()->where(Filter::in(EncargoField::IdTipoEnc, [8100, 8200, 1100]))->orderBy(EncargoField::IdEnc)
            )
        );
        $this->assertEquals(
            $repo->getEncargos(['id_ubi' => 'x', '_ordre' => 'id_enc'], ['id_ubi' => 'IS NOT NULL']),
            $repo->matching(Criteria::create()->where(Filter::isNotNull(EncargoField::IdUbi))->orderBy(EncargoField::IdEnc))
        );
    }

    public function test_encargo_texto_matching_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);

        $this->assertEquals(
            $repo->getEncargoTextos(['idioma' => 'es', '_ordre' => 'id_item']),
            $repo->matching(Criteria::create()->where(Filter::eq(EncargoTextoField::Idioma, 'es'))->orderBy(EncargoTextoField::IdItem))
        );
    }
}

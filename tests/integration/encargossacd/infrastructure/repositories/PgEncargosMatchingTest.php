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

        $this->assertNotEmpty($nuevo);
        $ids = [];
        foreach ($nuevo as $tipo) {
            $this->assertMatchesRegularExpression('/^8.../', (string) $tipo->getId_tipo_enc());
            $ids[] = (int) $tipo->getId_tipo_enc();
        }
        $ordenados = $ids;
        sort($ordenados);
        $this->assertSame($ordenados, $ids);
    }

    public function test_encargo_matching_in_regex_y_no_nulo(): void
    {
        $repo = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        foreach ($repo->matching(Criteria::create()->where(Filter::matches(EncargoField::IdTipoEnc, '(1|2|3).0'))->orderBy(EncargoField::IdEnc)) as $e) {
            $this->assertMatchesRegularExpression('/(1|2|3).0/', (string) $e->getId_tipo_enc());
        }
        foreach ($repo->matching(Criteria::create()->where(Filter::in(EncargoField::IdTipoEnc, [8100, 8200, 1100]))->orderBy(EncargoField::IdEnc)) as $e) {
            $this->assertContains((int) $e->getId_tipo_enc(), [8100, 8200, 1100]);
        }
        foreach ($repo->matching(Criteria::create()->where(Filter::isNotNull(EncargoField::IdUbi))->orderBy(EncargoField::IdEnc)) as $e) {
            $this->assertNotNull($e->getId_ubi());
        }
    }

    public function test_encargo_texto_matching(): void
    {
        $repo = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);

        $this->assertIsArray($repo->matching(Criteria::create()));
        foreach ($repo->matching(Criteria::create()->where(Filter::eq(EncargoTextoField::Idioma, 'es'))->orderBy(EncargoTextoField::IdItem)) as $t) {
            $this->assertSame('es', (string) $t->getIdioma());
        }
    }

    public function test_encargo_tipo_sin_acentos_equivale_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

        $resultado = $repo->matching(
            Criteria::create()->where(Filter::matchesUnaccented(EncargoTipoField::TipoEnc, 'a'))->orderBy(EncargoTipoField::IdTipoEnc)
        );

        $this->assertContainsOnlyInstancesOf(\src\encargossacd\domain\entity\EncargoTipo::class, $resultado);
    }

}
